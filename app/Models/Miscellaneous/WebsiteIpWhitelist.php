<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ip_address
 * @property string|null $asn
 * @property int $whitelist_asn
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteIpWhitelist whereWhitelistAsn($value)
 *
 * @mixin \Eloquent
 */
class WebsiteIpWhitelist extends Model
{
    protected $table = 'website_ip_whitelist';

    protected $guarded = ['id'];
}
