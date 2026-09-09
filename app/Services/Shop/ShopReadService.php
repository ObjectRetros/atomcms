<?php

namespace App\Services\Shop;

use App\Models\Shop\WebsiteShopCategory;
use App\Models\Shop\WebsiteShopPackage;

class ShopReadService
{
    /** @return array<string, mixed> */
    public function catalog(?WebsiteShopCategory $category = null): array
    {
        $packages = $category?->exists ? $category->packages()->orderBy('sort_order') : WebsiteShopPackage::orderBy('sort_order');

        return ['shopPackages' => $packages->with('items')->get(), 'categories' => WebsiteShopCategory::where('is_active', true)->whereHas('packages')->get()];
    }
}
