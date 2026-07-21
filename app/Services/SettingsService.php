<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    /** @var Collection<string, mixed>|null */
    private ?Collection $cachedSettings = null;

    /** @return Collection<string, mixed> */
    protected function settings(): Collection
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

    /**
     * @template TDefault
     *
     * @param  TDefault  $default
     *
     * @return string|TDefault
     */
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

    /** @return Collection<string, mixed> */
    private function fetchSettings(): Collection
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
