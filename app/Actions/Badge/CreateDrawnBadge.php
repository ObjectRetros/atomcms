<?php

namespace App\Actions\Badge;

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Exceptions\BadgePurchaseException;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use App\Rules\ValidGifBadge;
use App\Services\SettingsService;
use ErrorException;
use GdImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CreateDrawnBadge
{
    public function __construct(
        private readonly CurrencyRepository $currencies,
        private readonly SettingsService $settings,
    ) {}

    /**
     * Re-encode and store the badge, then charge and record it atomically.
     *
     * @param  array{badge_data: string, badge_name: string, badge_description: string}  $data
     */
    public function execute(User $user, array $data): WebsiteDrawBadge
    {
        $path = $this->storeImage($user, ValidGifBadge::decode($data['badge_data']));

        try {
            return DB::transaction(function () use ($user, $data, $path): WebsiteDrawBadge {
                $cost = (int) $this->settings->getOrDefault('drawbadge_currency_value', 150);
                $currencyType = (string) $this->settings->getOrDefault('drawbadge_currency_type', 'credits');
                $currency = CurrencyTypes::fromCurrencyName($currencyType);

                if ($currency === null || ! $this->currencies->deduct($user, $currency, $cost)) {
                    throw BadgePurchaseException::insufficientFunds($currencyType);
                }

                return $this->persist($user, $data, $path);
            });
        } catch (Throwable $exception) {
            // The charge or record rolled back; do not leave the file behind.
            $this->deleteStoredFile($path);

            throw $exception;
        }
    }

    private function storeImage(User $user, ?string $bytes): string
    {
        $directory = $this->settings->getOrDefault('badge_path_filesystem');

        if (! is_string($directory) || $directory === '' || ! is_dir($directory) || ! is_writable($directory) || $bytes === null) {
            throw BadgePurchaseException::pathNotConfigured();
        }

        $image = $this->decodeImage($bytes);
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $user->id . '_' . Str::ulid() . '.gif';

        try {
            $saved = imagegif($image, $path);
        } catch (Throwable) {
            $saved = false;
        } finally {
            imagedestroy($image);
        }

        if (! $saved) {
            $this->deleteStoredFile($path);

            throw BadgePurchaseException::saveFailed();
        }

        return $path;
    }

    private function decodeImage(string $bytes): GdImage
    {
        set_error_handler(static function (int $severity, string $message): never {
            throw new ErrorException($message, severity: $severity);
        });

        try {
            $image = imagecreatefromstring($bytes);
        } catch (Throwable) {
            throw BadgePurchaseException::saveFailed();
        } finally {
            restore_error_handler();
        }

        if ($image === false) {
            throw BadgePurchaseException::saveFailed();
        }

        return $image;
    }

    private function deleteStoredFile(string $path): void
    {
        if (! is_file($path)) {
            return;
        }

        try {
            if (! unlink($path)) {
                Log::error('Failed to remove a drawn badge file.', ['path' => $path]);
            }
        } catch (Throwable $exception) {
            Log::error('Failed to remove a drawn badge file.', [
                'path' => $path,
                'exception' => $exception,
            ]);
        }
    }

    /**
     * @param  array{badge_name: string, badge_description: string}  $data
     */
    private function persist(User $user, array $data, string $path): WebsiteDrawBadge
    {
        return WebsiteDrawBadge::create([
            'user_id' => $user->id,
            'badge_path' => $path,
            'badge_url' => $this->badgeUrl($path),
            'badge_name' => $data['badge_name'],
            'badge_desc' => $data['badge_description'],
        ]);
    }

    private function badgeUrl(string $path): string
    {
        $baseUrl = (string) $this->settings->getOrDefault('badges_path', '/badges/');

        return rtrim($baseUrl, '/') . '/' . basename($path);
    }
}
