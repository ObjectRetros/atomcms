<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowInventoryStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowLay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowMarketplaceSell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowRecycle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowSit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowTrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereAllowWalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereClothingOnWalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereCustomparams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereEffectIdFemale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereEffectIdMale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereInteractionModesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereInteractionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereMultiheight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition wherePublicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereRare($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereSpriteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereStackHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereVendingIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemDefinition whereWidth($value)
 *
 * @mixin \Eloquent
 */
class ItemDefinition extends Model
{
    use HasFactory;

    protected $table = 'items_base';
}
