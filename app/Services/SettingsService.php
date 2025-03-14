<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsitePermission;
use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    public ?Collection $settings;

    public function __construct()
    {
        Cache::remember('website_settings', now()->addMinutes(5), function () {
            return Schema::hasTable('website_settings') ? WebsiteSetting::all()->pluck('value', 'key') : collect();
        });

        $this->settings = Cache::get('website_settings');
    }

    public function getOrDefault(string $settingName, ?string $default = null): string
    {
        return (string)$this->settings->get($settingName, $default);
    }
}
