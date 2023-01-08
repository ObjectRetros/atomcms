<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteShopProductFeature extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopProduct::class, 'id', 'shop_product_id');
    }
}