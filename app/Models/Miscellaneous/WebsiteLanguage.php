<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $country_code
 * @property string $language
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteLanguage whereLanguage($value)
 *
 * @mixin \Eloquent
 */
class WebsiteLanguage extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
