<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WebsiteShopItem extends Model
{
    protected $guarded = [];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteShopPackage::class, 'website_shop_package_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
