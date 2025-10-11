<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteShopArticleFeature extends Model
{
    protected $guarded = ['id'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopArticle::class, 'article_id', 'id');
    }
}
