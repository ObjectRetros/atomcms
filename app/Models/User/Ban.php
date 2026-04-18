<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string $machine_id
 * @property int $user_staff_id
 * @property int $timestamp
 * @property int $ban_expire
 * @property string $ban_reason
 * @property string $type Account is the entry in the users table banned.
 *                        IP is any client that connects with that IP.
 *                        Machine is the computer that logged in.
 *                        Super is all of the above.
 * @property int $cfh_topic
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read User|null $staff
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereBanExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereBanReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereCfhTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ban whereUserStaffId($value)
 *
 * @mixin \Eloquent
 */
class Ban extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_staff_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'ip', 'ban_expire', 'ban_reason', 'type']);
    }
}
