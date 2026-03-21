<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteShopPurchase extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopPackage::class, 'website_shop_package_id');
    }

    public function giftedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gifted_to');
    }
}
