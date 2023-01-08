<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteShopVoucher extends Model
{
    protected $guarded = ['id'];

    public function usedVouchers(): HasMany
    {
        return $this->hasMany(WebsiteShopUsedVoucher::class, 'code_id');
    }
}