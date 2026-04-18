<?php

namespace App\Models\Game\Furniture;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $caption_save
 * @property string $caption
 * @property string $page_layout
 * @property int $icon_color
 * @property int $icon_image
 * @property int $min_rank
 * @property int $order_num
 * @property string $visible
 * @property string $enabled
 * @property string $club_only
 * @property string $vip_only
 * @property string $page_headline
 * @property string $page_teaser
 * @property string|null $page_special Gold Bubble: catalog_special_txtbg1 // Speech Bubble: catalog_special_txtbg2 // Place normal text in page_text_teaser
 * @property string|null $page_text1
 * @property string|null $page_text2
 * @property string|null $page_text_details
 * @property string|null $page_text_teaser
 * @property int|null $room_id
 * @property string $includes Example usage: 1;2;3
 *                            This will include page 1, 2 and 3 in the current page.
 *                            Note that permissions are only used for the current entry.
 * @property-read Collection<int, CatalogItem> $catalogItems
 * @property-read int|null $catalog_items_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereCaptionSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereClubOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereIconColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereIconImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereIncludes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereMinRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereOrderNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageHeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageTeaser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageText1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageText2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageTextDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage wherePageTextTeaser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereVipOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CatalogPage whereVisible($value)
 *
 * @mixin \Eloquent
 */
class CatalogPage extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'page_id');
    }
}
