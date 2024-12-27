<?php

namespace App\Models\Shop;

use App\Models\Game\Furniture\ItemBase;
use App\Models\Game\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class WebsiteShopArticle extends Model
{
    protected $guarded = ['id'];

    public function furniItems(): Collection
    {
        if (!$this->furniture) {
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
        return $this->HasMany(WebsiteShopArticleFeature::class, 'article_id', 'id');
    }

    public function price(): float|int
    {
        if ($this->costs < 100) {
            return 1;
        }

        return $this->costs / 100;
    }
}
