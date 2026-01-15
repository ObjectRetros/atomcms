<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    protected function settings()
    {
        // Don't cache if installation is not complete
        if ($this->isInstallationIncomplete()) {
            return $this->fetchSettings();
        }

        return Cache::rememberForever('website_settings', function () {
            return $this->fetchSettings();
        });
    }

    public function getOrDefault(string $key, mixed $default = null): mixed
    {
        return $this->settings()->get($key, $default);
    }

    public static function clearCache(): void
    {
        Cache::forget('website_settings');
    }

    private function isInstallationIncomplete(): bool
    {
        try {
            // Check if installation table exists and if installation is completed
            if (! Schema::hasTable('website_installation')) {
                return true;
            }

            $installation = DB::table('website_installation')->first();

            return ! $installation || ! $installation->completed;
        } catch (Throwable) {
            return true;
        }
    }

    private function fetchSettings()
    {
        if (! Schema::hasTable('website_settings')) {
            return collect();
        }

        return WebsiteSetting::query()->pluck('value', 'key');
    }
}
