<?php

namespace App\Console\Commands;

use App\Models\WebsiteAd;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ImportAdsData extends Command
{
    protected $signature = 'import:ads-data';

    protected $description = 'Import ads data from the filesystem';

    private const CHUNK_SIZE = 100;

    private const ALLOWED_EXTENSIONS = ['jpeg', 'jpg', 'png', 'gif'];

    public function handle(SettingsService $settingsService): void
    {
        $adsPath = $settingsService->getOrDefault('ads_path_filesystem');

        if (! $this->validatePath($adsPath)) {
            return;
        }

        $files = $this->getImageFiles($adsPath);

        if (empty($files)) {
            $this->warn('No valid image files found in the ads directory.');

            return;
        }

        $this->processFiles($files);

        $this->info('Ads data import completed successfully.');
    }

    private function validatePath(?string $adsPath): bool
    {
        if (empty($adsPath)) {
            $this->error('Ads path is not configured in website_settings.');

            return false;
        }

        if (! is_dir($adsPath)) {
            $this->error("The ads path '{$adsPath}' does not exist in the filesystem.");

            return false;
        }

        return true;
    }

    private function getImageFiles(string $adsPath): array
    {
        return array_filter(scandir($adsPath), function ($file) use ($adsPath) {
            $filePath = $adsPath . DIRECTORY_SEPARATOR . $file;

            return is_file($filePath) &&
                   in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS);
        });
    }

    private function processFiles(array $files): void
    {
        // Get existing images to avoid duplicates
        $existingImages = WebsiteAd::pluck('image')->toArray();

        $newFiles = Collection::make($files)
            ->filter(fn ($file) => ! in_array($file, $existingImages))
            ->map(fn ($file) => ['image' => $file])
            ->values();

        $skippedCount = count($files) - $newFiles->count();
        if ($skippedCount > 0) {
            $this->warn("Skipped {$skippedCount} existing files.");
        }

        $newFiles->chunk(self::CHUNK_SIZE)->each(function ($chunk) {
            WebsiteAd::insert($chunk->toArray());
            $this->info('Processed ' . $chunk->count() . ' files.');
        });
    }
}
