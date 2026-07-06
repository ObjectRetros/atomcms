<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $website_shop_package_id
 * @property int $website_shop_item_id
 * @property int $quantity
 */
class WebsiteShopPackageItem extends Pivot
{
    protected $table = 'website_shop_package_items';

    public $incrementing = true;

    /**
     * @return BelongsTo<WebsiteShopPackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopPackage::class, 'website_shop_package_id');
    }

    /**
     * @return BelongsTo<WebsiteShopItem, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopItem::class, 'website_shop_item_id');
    }
}
