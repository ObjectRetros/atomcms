<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $badge_key
 * @property string $badge_name
 * @property string $badge_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereBadgeDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereBadgeKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereBadgeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteBadge whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_key',
        'badge_name',
        'badge_description',
    ];
}
