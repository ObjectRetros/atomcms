<?php

namespace App\Actions\Badge;

use App\Actions\DeductCurrency;
use App\Exceptions\BadgePurchaseException;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use App\Rules\ValidGifBadge;
use App\Services\SettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CreateDrawnBadge
{
    public function __construct(
        private readonly DeductCurrency $deductCurrency,
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

                if (! $this->deductCurrency->execute($user, $currencyType, $cost)) {
                    throw BadgePurchaseException::insufficientFunds($currencyType);
                }

                return $this->persist($user, $data, $path);
            });
        } catch (Throwable $exception) {
            // The charge or record rolled back; do not leave the file behind.
            @unlink($path);

            throw $exception;
        }
    }

    private function storeImage(User $user, ?string $bytes): string
    {
        $directory = $this->settings->getOrDefault('badge_path_filesystem');

        if (! $directory || $bytes === null) {
            throw BadgePurchaseException::pathNotConfigured();
        }

        $image = @imagecreatefromstring($bytes);

        if ($image === false) {
            throw BadgePurchaseException::saveFailed();
        }

        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $user->id . '_' . Str::ulid() . '.gif';
        $saved = imagegif($image, $path);
        imagedestroy($image);

        if (! $saved) {
            throw BadgePurchaseException::saveFailed();
        }

        return $path;
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
