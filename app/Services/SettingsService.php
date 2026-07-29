<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    /** @var Collection<string, mixed>|null */
    private ?Collection $cachedSettings = null;

    public function __construct(private readonly InstallationService $installation) {}

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

    /**
     * Drop the persisted and memoized settings so the next read refetches.
     * Instance-level, so objects already holding the singleton see fresh
     * values instead of a stale forgotten instance.
     */
    public function refresh(): void
    {
        Cache::forget('website_settings');
        $this->cachedSettings = null;
    }

    private function isInstallationIncomplete(): bool
    {
        return ! $this->installation->isComplete();
    }

    /** @return Collection<string, mixed> */
    private function fetchSettings(): Collection
    {
        if (! Schema::hasTable('website_settings')) {
            return collect();
        }

        return WebsiteSetting::query()->pluck('value', 'key');
    }
}
