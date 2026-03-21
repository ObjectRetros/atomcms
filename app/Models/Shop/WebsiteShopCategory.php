<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteShopCategory extends Model
{
    protected $guarded = [];

    public function articles(): HasMany
    {
        return $this->hasMany(WebsiteShopArticle::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(WebsiteShopPackage::class);
    }
}
