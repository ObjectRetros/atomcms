<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SettingsService
{
    public ?Collection $settings;

    public function __construct()
    {
        try {
            Cache::remember('website_settings', now()->addMinutes(5), function () {
                return Schema::hasTable('website_settings') ? WebsiteSetting::all()->pluck('value', 'key') : collect();
            });

            $this->settings = Cache::get('website_settings');
        } catch (Throwable $e) {
            $this->settings = collect();
        }
    }

    public function getOrDefault(string $settingName, ?string $default = null): string
    {
        if (! $this->settings) {
            return (string) $default;
        }

        return (string) $this->settings->get($settingName, $default);
    }
}
