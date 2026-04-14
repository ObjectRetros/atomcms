<?php

namespace App\Models\Shop;

use App\Models\Game\Furniture\ItemBase;
use App\Models\Game\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int|null $website_shop_category_id
 * @property string $name
 * @property string $info
 * @property string $icon_url
 * @property string $color
 * @property int $costs
 * @property int|null $give_rank
 * @property int|null $credits
 * @property int|null $duckets
 * @property int|null $diamonds
 * @property string|null $badges
 * @property string|null $furniture
 * @property int $position
 * @property int $is_giftable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WebsiteShopArticleFeature> $features
 * @property-read int|null $features_count
 * @property-read Permission|null $rank
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereBadges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereDiamonds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereDuckets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereFurniture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereGiveRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereIsGiftable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteShopArticle whereWebsiteShopCategoryId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteShopArticle extends Model
{
    protected $guarded = ['id'];

    public function furniItems(): Collection
    {
        if (! $this->furniture) {
            return collect();
        }

        $furniture = json_decode($this->furniture, true);
        $furnitureIds = array_column($furniture, 'item_id');

        return ItemBase::whereIn('id', $furnitureIds)->get();
    }

    public function rank(): HasOne
    {
        return $this->hasOne(Permission::class, 'id', 'give_rank');
    }

    public function features(): HasMany
    {
        return $this->hasMany(WebsiteShopArticleFeature::class, 'article_id', 'id');
    }

    public function price(): float|int
    {
        if ($this->costs < 100) {
            return 1;
        }

        return $this->costs / 100;
    }
}
