<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WebsiteShopPackageItem extends Pivot
{
    protected $table = 'website_shop_package_items';

    public $incrementing = true;

    public function package(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopPackage::class, 'website_shop_package_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopItem::class, 'website_shop_item_id');
    }
}
