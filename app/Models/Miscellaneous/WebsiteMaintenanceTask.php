<?php

namespace App\Models\Miscellaneous;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $task
 * @property int $completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteMaintenanceTask whereUserId($value)
 *
 * @mixin \Eloquent
 */
class WebsiteMaintenanceTask extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
