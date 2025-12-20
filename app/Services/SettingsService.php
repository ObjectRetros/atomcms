<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    private const NULL_SENTINEL = '__CACHE_NULL__';

    protected function settings()
    {
        return Cache::rememberForever('website_settings', function () {
            if (! Schema::hasTable('website_settings')) {
                return collect();
            }

            return WebsiteSetting::query()
                ->pluck('value', 'key')
                ->map(fn ($value) => $value === null ? self::NULL_SENTINEL : $value);
        });
    }

    public function getOrDefault(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings();

        if (! $settings->has($key)) {
            return $default;
        }

        $value = $settings->get($key);

        return $value === self::NULL_SENTINEL ? $default : $value;
    }

    public static function clearCache(): void
    {
        Cache::forget('website_settings');
    }
}
