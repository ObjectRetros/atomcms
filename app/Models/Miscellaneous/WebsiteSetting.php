<?php

namespace App\Models\Miscellaneous;

use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::saved(fn () => SettingsService::clearCache());
        static::deleted(fn () => SettingsService::clearCache());
    }
}
