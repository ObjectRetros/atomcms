<?php

namespace App\Console\Commands;

use App\Models\WebsiteAd;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportAdsData extends Command
{
    protected $signature = 'import:ads-data';
    protected $description = 'Import ads data from the filesystem';

    public function handle(SettingsService $settingsService): void
    {
        $adsPath = $settingsService->getOrDefault('ads_path_filesystem');

        if (empty($adsPath)) {
            $this->error('Ads path is not configured in website_settings.');
            return;
        }

        if (!is_dir($adsPath)) {
            $this->error("The ads path '{$adsPath}' does not exist in the filesystem.");
            return;
        }

        $files = array_filter(scandir($adsPath), function ($file) use ($adsPath) {
            $filePath = $adsPath . DIRECTORY_SEPARATOR . $file;
            return is_file($filePath) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'gif']);
        });

        if (empty($files)) {
            $this->warn('No valid image files found in the ads directory.');
            return;
        }

        foreach ($files as $file) {
            $fileName = basename($file);

            $existingAd = WebsiteAd::where('image', $fileName)->first();

            if (!$existingAd) {
                WebsiteAd::create([
                    'image' => $fileName,
                ]);
                $this->info("Imported: {$fileName}");
            } else {
                $this->warn("Skipped (already exists): {$fileName}");
            }
        }

        $this->info('Ads data import completed successfully.');
    }
}