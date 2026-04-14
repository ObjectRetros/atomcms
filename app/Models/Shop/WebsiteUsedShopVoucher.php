<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $voucher_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteUsedShopVoucher> $used
 * @property-read int|null $used_count
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

    public function used(): HasMany
    {
        return $this->hasMany(WebsiteUsedShopVoucher::class, 'voucher_id');
    }
}
