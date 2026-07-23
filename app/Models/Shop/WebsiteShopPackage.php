<?php

namespace App\Models\Shop;

use App\Support\StorefrontMoney;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $website_shop_category_id
 * @property int $sort_order
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property int $price Price in cents
 * @property int|null $min_rank
 * @property int|null $max_rank
 * @property int|null $limit_per_user
 * @property int|null $stock Null means unlimited
 * @property bool $is_giftable
 * @property Carbon|null $available_from
 * @property Carbon|null $available_to
 */
class WebsiteShopPackage extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_giftable' => 'boolean',
            'available_from' => 'datetime',
            'available_to' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<WebsiteShopCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopCategory::class, 'website_shop_category_id');
    }

    /**
     * @return BelongsToMany<WebsiteShopItem, $this, WebsiteShopPackageItem>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteShopItem::class, 'website_shop_package_items')
            ->using(WebsiteShopPackageItem::class)
            ->withPivot('id', 'quantity')
            ->withTimestamps();
    }

    /**
     * @return HasMany<WebsiteShopPackageItem, $this>
     */
    public function packageItems(): HasMany
    {
        return $this->hasMany(WebsiteShopPackageItem::class);
    }

    /**
     * @return HasMany<WebsiteShopPurchase, $this>
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(WebsiteShopPurchase::class);
    }

    /**
     * The package price as a money object; named to avoid colliding with
     * the integer `price` attribute.
     */
    public function priceMoney(): Money
    {
        return StorefrontMoney::fromMinor($this->price);
    }

    public function formattedPrice(): string
    {
        return (string) $this->priceMoney();
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
