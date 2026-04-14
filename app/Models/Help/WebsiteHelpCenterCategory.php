<?php

namespace App\Models\Help;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $position
 * @property string|null $image_url
 * @property string|null $button_text
 * @property string|null $button_url
 * @property string $button_color
 * @property string $button_border_color
 * @property int $small_box
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereButtonBorderColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereButtonColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereButtonText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereButtonUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHelpCenterCategory whereSmallBox($value)
 *
 * @mixin \Eloquent
 */
class WebsiteHelpCenterCategory extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
