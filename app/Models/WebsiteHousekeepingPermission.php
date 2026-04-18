<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $permission
 * @property int $min_rank
 * @property string|null $description Describes the permissions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission whereMinRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteHousekeepingPermission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteHousekeepingPermission extends Model
{
    protected $guarded = ['id'];
}
