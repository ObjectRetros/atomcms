<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $voucher_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteUsedShopVoucher whereVoucherId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteUsedShopVoucher extends Model
{
    protected $guarded = ['id'];
}
