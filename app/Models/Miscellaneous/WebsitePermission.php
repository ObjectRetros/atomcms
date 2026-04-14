<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $permission
 * @property string|null $min_rank
 * @property string|null $description Explanation on what the permission is used for
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission whereMinRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsitePermission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsitePermission extends Model
{
    protected $guarded = ['id'];
}
