<?php

namespace App\Models\Game\Furniture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $sprite_id
 * @property string $item_name
 * @property string $public_name
 * @property int $width
 * @property int $length
 * @property float $stack_height
 * @property int $allow_stack
 * @property int $allow_sit
 * @property int $allow_lay
 * @property int $allow_walk
 * @property int $allow_gift
 * @property int $allow_trade
 * @property int $allow_recycle
 * @property int $allow_marketplace_sell
 * @property int $allow_inventory_stack
 * @property string $type
 * @property string $interaction_type
 * @property int $interaction_modes_count
 * @property string $vending_ids
 * @property string $multiheight
 * @property string $customparams
 * @property int $effect_id_male
 * @property int $effect_id_female
 * @property string $clothing_on_walk
 * @property string|null $page_id
 * @property string|null $rare
 * @property-read Collection<int, CatalogItem> $catalogItems
 * @property-read int|null $catalog_items_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowInventoryStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowLay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowMarketplaceSell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowRecycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowSit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowTrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereAllowWalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereClothingOnWalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereCustomparams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereEffectIdFemale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereEffectIdMale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereInteractionModesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereInteractionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereMultiheight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase wherePublicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereRare($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereSpriteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereStackHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereVendingIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemBase whereWidth($value)
 *
 * @mixin \Eloquent
 */
class ItemBase extends Model
{
    protected $table = 'items_base';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function icon(): string
    {
        return sprintf('%s/%s_icon.png', setting('furniture_icons_path'), $this->item_name);
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'item_ids', 'id');
    }
}
