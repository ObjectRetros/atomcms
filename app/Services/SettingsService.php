<?php

namespace App\Services;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    public ?Collection $settings;

    public function __construct()
    {
        $this->settings = Schema::hasTable('website_settings') ? WebsiteSetting::all()->pluck('value', 'key') : collect();
    }

    public function getOrDefault(string $settingName, ?string $default = null): string
    {
        return (string)$this->settings->get($settingName, $default);
    }
}
