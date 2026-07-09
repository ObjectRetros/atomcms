<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id The buyer
 * @property int $website_shop_package_id
 * @property int|null $gifted_to Recipient when the purchase was a gift
 */
class WebsiteShopPurchase extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<WebsiteShopPackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopPackage::class, 'website_shop_package_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function giftedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gifted_to');
    }
}
