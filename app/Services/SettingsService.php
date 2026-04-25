<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    private ?Collection $cachedSettings = null;

    protected function settings()
    {
        if ($this->cachedSettings !== null) {
            return $this->cachedSettings;
        }

        if ($this->isInstallationIncomplete()) {
            $this->cachedSettings = $this->fetchSettings();

            return $this->cachedSettings;
        }

        $this->cachedSettings = collect(Cache::rememberForever('website_settings', function () {
            return $this->fetchSettings()->toArray();
        }));

        return $this->cachedSettings;
    }

    public function getOrDefault(string $key, mixed $default = null): mixed
    {
        return $this->settings()->get($key, $default);
    }

    public static function clearCache(): void
    {
        Cache::forget('website_settings');
        app()->forgetInstance(self::class);
    }

    private function isInstallationIncomplete(): bool
    {
        try {
            return ! app(InstallationService::class)->isComplete();
        } catch (Throwable) {
            return true;
        }
    }

    private function fetchSettings()
    {
        try {
            if (! Schema::hasTable('website_settings')) {
                return collect();
            }

            return WebsiteSetting::query()->pluck('value', 'key');
        } catch (Throwable) {
            return collect();
        }
    }
}
