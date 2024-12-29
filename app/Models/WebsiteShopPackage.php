<?php

namespace App\Models;

use App\Models\Shop\WebsiteShopCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WebsiteShopPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'available_from' => 'datetime',
        'available_to' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopCategory::class, 'website_shop_category_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteShopItem::class, 'website_shop_package_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }


}
