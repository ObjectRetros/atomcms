<?php

namespace App\Models\Community\RareValue;

use App\Models\Game\Furniture\CatalogItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $category_id
 * @property int|null $item_id
 * @property string $name
 * @property string|null $credit_value
 * @property string|null $currency_value
 * @property int $currency_type
 * @property string $furniture_icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteRareValueCategory $category
 * @property-read CatalogItem|null $item
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereCreditValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereCurrencyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereCurrencyValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereFurnitureIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRareValue whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteRareValue extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts()
    {
        return [
            'currency_type' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteRareValueCategory::class, 'category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'item_id', 'item_ids');
    }

    public function isLimitedEdition(): bool
    {
        if (is_null($this->item)) {
            return false;
        }

        return $this->item->limited_stack > 0;
    }
}
