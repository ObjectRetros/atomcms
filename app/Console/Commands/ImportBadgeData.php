<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebsiteBadgedata;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportBadgeData extends Command
{
    protected $signature = 'import:badge-data';
    protected $description = 'Import badge data from JSON file';

    public function handle()
    {
        $jsonPath = '/var/www/camwijs.eu/Gamedata/config/ExternalTexts.json';
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