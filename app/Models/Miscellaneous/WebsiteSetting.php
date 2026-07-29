<?php

namespace App\Models\Miscellaneous;

use App\Services\SettingsService;
use App\Support\CommunityCache;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $comment Add an explanation of the setting does
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteSetting whereValue($value)
 *
 * @mixin \Eloquent
 */
class WebsiteSetting extends Model
{
    public const OPTIONAL_INSTALLATION_KEYS = [
        'flash_external_texts_file',
        'seo_description',
        'seo_keywords',
    ];

    protected $guarded = [];

    public $timestamps = false;

    public function isRequiredDuringInstallation(): bool
    {
        return ! in_array($this->key, self::OPTIONAL_INSTALLATION_KEYS, true);
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            app(SettingsService::class)->refresh();
            CommunityCache::forgetAll();
        });
        static::deleted(function (): void {
            app(SettingsService::class)->refresh();
            CommunityCache::forgetAll();
        });
    }
}
