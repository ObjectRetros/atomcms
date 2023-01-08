<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PhpParser\Node\Expr\Cast\Bool_;

class WebsiteShopProduct extends Model
{
    protected $guarded = ['id'];

    public function features(): HasOne
    {
        return $this->hasOne(WebsiteShopProductFeature::class, 'shop_product_id');
    }

    public function price(): int
    {
        if (!$this->isOnSale()) {
            return $this->price;
        }

        $total = $this->price - ceil(($this->price * $this->percentage_off) / 100);

        // Round up 1 in-case the price is below 0 when on sale
        return $total > 0 ? $total : 1;
    }

    public function isOnSale(): bool
    {
        return $this->percentage_off && (now()->greaterThanOrEqualTo($this->sale_start) && now()->lessThan($this->sale_end));
    }
}