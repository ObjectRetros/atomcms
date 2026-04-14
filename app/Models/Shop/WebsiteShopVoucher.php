<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property int $max_uses
 * @property int $use_count
 * @property int $amount
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereMaxUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopVoucher whereUseCount($value)
 *
 * @mixin \Eloquent
 */
class WebsiteShopVoucher extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
