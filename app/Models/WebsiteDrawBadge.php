<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $badge_path
 * @property string $badge_url
 * @property string $badge_name
 * @property string $badge_desc
 * @property int $published
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereBadgeDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereBadgeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereBadgePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereBadgeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteDrawBadge whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteDrawBadge extends Model
{
    protected $table = 'website_drawbadges';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
