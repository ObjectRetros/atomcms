<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WebsiteShopUsedVoucher extends Model
{
    protected $guarded = ['id'];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopVoucher::class, 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}