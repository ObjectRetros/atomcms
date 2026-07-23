<?php

namespace App\Models\Shop;

use App\Enums\ShopItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A grantable reward (currency, furniture, badge or rank) that packages bundle.
 *
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property ShopItemType $type
 * @property string $type_value Currency "type:amount" (credits:100), furniture item id, badge code or rank id
 * @property bool $is_active
 * @property-read WebsiteShopPackageItem|null $pivot Present when accessed through a package's items() relation
 */
class WebsiteShopItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => ShopItemType::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<WebsiteShopPackage, $this, WebsiteShopPackageItem>
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(WebsiteShopPackage::class, 'website_shop_package_items')
            ->using(WebsiteShopPackageItem::class)
            ->withPivot('id', 'quantity')
            ->withTimestamps();
    }
}
