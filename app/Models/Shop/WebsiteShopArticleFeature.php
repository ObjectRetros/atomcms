<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $article_id
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteShopArticle $article
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticleFeature whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteShopArticleFeature extends Model
{
    protected $guarded = ['id'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(WebsiteShopArticle::class, 'article_id', 'id');
    }
}
