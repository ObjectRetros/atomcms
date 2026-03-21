<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteShopPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_giftable' => 'boolean',
            'available_from' => 'datetime',
            'available_to' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopCategory::class, 'website_shop_category_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteShopItem::class, 'website_shop_package_items')
            ->withPivot('id', 'quantity')
            ->withTimestamps();
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(WebsiteShopPackageItem::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(WebsiteShopPurchase::class);
    }

    public function priceInDollars(): float|int
    {
        if ($this->price < 100) {
            return 1;
        }

        return $this->price / 100;
    }

    public function isAvailable(): bool
    {
        if ($this->available_from && $this->available_from->isFuture()) {
            return false;
        }

        if ($this->available_to && $this->available_to->isPast()) {
            return false;
        }

        if ($this->stock !== null && $this->stock <= 0) {
            return false;
        }

        return true;
    }
}
