<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebsiteBadgedata;
use App\Services\SettingsService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportBadgeData extends Command
{
    protected $signature = 'import:badge-data';
    protected $description = 'Import badge data from JSON file';

    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        parent::__construct();
        $this->settingsService = $settingsService;
    }

    public function handle()
    {
        // Get the JSON file path from the database
        $jsonPath = $this->settingsService->getOrDefault('nitro_external_texts_file');

        if (empty($jsonPath)) {
            $this->error('The JSON file path is not configured in the website settings.');
            return;
        }

        if (!file_exists($jsonPath)) {
            $this->error('The JSON file does not exist at the specified path: ' . $jsonPath);
            return;
        }

        $jsonData = File::json($jsonPath);

        foreach ($jsonData as $key => $value) {
            if (str_starts_with($key, 'badge_desc_')) {
                WebsiteBadgedata::updateOrCreate(
                    ['badge_key' => $key],
                    [
                        'badge_name' => str_replace('badge_desc_', '', $key),
                        'badge_description' => $value,
                    ]
                );
            }
        }

        $this->info('Badge data imported successfully.');
    }
}