<?php

namespace App\Models\Miscellaneous;

use App\Services\SettingsService;
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
    protected $guarded = [];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::saved(fn () => SettingsService::clearCache());
        static::deleted(fn () => SettingsService::clearCache());
    }
}
