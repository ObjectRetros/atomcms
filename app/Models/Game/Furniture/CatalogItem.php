<?php

namespace App\Models\Game\Furniture;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $item_ids
 * @property int $page_id
 * @property int $offer_id
 * @property int $song_id
 * @property int $order_number
 * @property string $catalog_name
 * @property int $cost_credits
 * @property int $cost_points
 * @property int $points_type 0 for duckets; 5 for diamonds; and any seasonal/GOTW currencies you have in your emu_settings table.
 * @property int $amount
 * @property int $limited_sells This automatically logs from the emu; do not change it.
 * @property int $limited_stack Change this number to make the item limited.
 * @property string $extradata
 * @property string|null $badge
 * @property string $have_offer
 * @property string $club_only
 * @property string|null $rate
 * @property-read ItemBase|null $itemBase
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereCatalogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereClubOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereCostCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereCostPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereExtradata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereHaveOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereItemIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereLimitedSells($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereLimitedStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem wherePointsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogItem whereSongId($value)
 *
 * @mixin \Eloquent
 */
class CatalogItem extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function itemBase(): BelongsTo
    {
        return $this->belongsTo(ItemBase::class, 'item_ids', 'id');
    }
}
