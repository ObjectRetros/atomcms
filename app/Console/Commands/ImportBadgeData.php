<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebsiteBadgedata;
use App\Services\SettingsService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ImportBadgeData extends Command
{
    protected $signature = 'import:badge-data';
    protected $description = 'Import badge data from JSON file';

    private const CHUNK_SIZE = 100;
    private const BADGE_PREFIX = 'badge_desc_';

    public function __construct(
        private readonly SettingsService $settingsService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $jsonPath = $this->settingsService->getOrDefault('nitro_external_texts_file');

        if (!$this->validateJsonFile($jsonPath)) {
            return;
        }

        try {
            $this->processBadgeData($jsonPath);
            $this->info('Badge data imported successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to import badge data: ' . $e->getMessage());
            $this->error('Failed to import badge data. Check the logs for details.');
        }
    }

    private function validateJsonFile(?string $jsonPath): bool 
    {
        if (empty($jsonPath)) {
            $this->error('The JSON file path is not configured in the website settings.');
            return false;
        }

        if (!file_exists($jsonPath)) {
            $this->error('The JSON file does not exist at the specified path: ' . $jsonPath);
            return false;
        }

        return true;
    }

    private function processBadgeData(string $jsonPath): void 
    {
        $badgeData = Collection::make(File::json($jsonPath))
            ->filter(fn($value, $key) => str_starts_with($key, self::BADGE_PREFIX))
            ->map(fn($value, $key) => [
                'badge_key' => $key,
                'badge_name' => str_replace(self::BADGE_PREFIX, '', $key),
                'badge_description' => $value,
            ])
            ->values();

        $badgeData->chunk(self::CHUNK_SIZE)->each(function ($chunk) {
            WebsiteBadgedata::upsert(
                $chunk->toArray(),
                ['badge_key'],
                ['badge_name', 'badge_description']
            );
            
            $this->info("Processed " . $chunk->count() . " badges.");
        });
    }
}
