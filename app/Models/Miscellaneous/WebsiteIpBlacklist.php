<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ip_address
 * @property string|null $asn
 * @property int $blacklist_asn
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereBlacklistAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpBlacklist whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteIpBlacklist extends Model
{
    protected $table = 'website_ip_blacklist';

    protected $guarded = ['id'];
}
