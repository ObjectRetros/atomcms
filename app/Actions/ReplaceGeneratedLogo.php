<?php

namespace App\Actions;

use App\Models\Miscellaneous\WebsiteSetting;
use App\Services\SettingsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class ReplaceGeneratedLogo
{
    private const DIRECTORY = 'assets/images/generated-logos';

    /** @var list<string> */
    private const EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    public function __construct(private readonly SettingsService $settings) {}

    public function execute(UploadedFile $file): string
    {
        return Cache::lock('generated-logo-replacement', 10)->block(
            5,
            fn (): string => $this->replace($file),
        );
    }

    private function replace(UploadedFile $file): string
    {
        $extension = $file->guessExtension();

        if (! is_string($extension) || ! in_array($extension, self::EXTENSIONS, true)) {
            throw new RuntimeException('The generated logo has an unsupported image type.');
        }

        $directory = public_path(self::DIRECTORY);
        File::ensureDirectoryExists($directory);

        $previousPath = $this->managedPath($this->settings->getOrDefault('cms_logo'));
        $filename = Str::uuid() . '.' . $extension;
        $storedFile = $file->move($directory, $filename);
        $storedPath = $storedFile->getPathname();
        $publicPath = '/' . self::DIRECTORY . '/' . $filename;

        try {
            WebsiteSetting::query()->updateOrCreate(['key' => 'cms_logo'], [
                'value' => $publicPath,
                'comment' => 'CMS logo path',
            ]);

            SettingsService::clearCache();
        } catch (Throwable $exception) {
            $this->delete($storedPath, 'new');

            throw $exception;
        }

        if ($previousPath !== null && $previousPath !== $storedPath) {
            $this->delete($previousPath, 'previous');
        }

        return $publicPath;
    }

    private function managedPath(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $relativePath = ltrim(str_replace('\\', '/', $value), '/');

        if (dirname($relativePath) !== self::DIRECTORY) {
            return null;
        }

        $filename = basename($relativePath);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $identifier = pathinfo($filename, PATHINFO_FILENAME);

        if (! Str::isUuid($identifier) || ! in_array($extension, self::EXTENSIONS, true)) {
            return null;
        }

        return public_path($relativePath);
    }

    private function delete(string $path, string $kind): void
    {
        if (! is_file($path)) {
            return;
        }

        if (! File::delete($path)) {
            Log::warning("Unable to delete {$kind} generated logo.", ['path' => $path]);
        }
    }
}
