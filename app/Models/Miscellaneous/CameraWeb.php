<?php

namespace App\Models\Miscellaneous;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property \Illuminate\Support\Carbon $timestamp
 * @property string $url
 * @property int $visible
 * @property-read mixed $formatted_date
 * @property-read Room|null $room
 * @property-read User|null $user
 *
 * @method static Builder<static>|CameraWeb newModelQuery()
 * @method static Builder<static>|CameraWeb newQuery()
 * @method static Builder<static>|CameraWeb period($period)
 * @method static Builder<static>|CameraWeb query()
 * @method static Builder<static>|CameraWeb whereId($value)
 * @method static Builder<static>|CameraWeb whereRoomId($value)
 * @method static Builder<static>|CameraWeb whereTimestamp($value)
 * @method static Builder<static>|CameraWeb whereUrl($value)
 * @method static Builder<static>|CameraWeb whereUserId($value)
 * @method static Builder<static>|CameraWeb whereVisible($value)
 *
 * @mixin \Eloquent
 */
class CameraWeb extends Model
{
    protected $table = 'camera_web';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function scopePeriod(Builder $query, $period): void
    {
        if ($period == 'today') {
            $query->where('timestamp', '>=', Carbon::today()->timestamp);
        }

        if ($period == 'last_week') {
            $query->whereBetween('timestamp', [now()->subWeek()->timestamp, now()->timestamp]);
        }

        if ($period == 'last_month') {
            $query->whereBetween('timestamp', [now()->subMonth()->timestamp, now()->timestamp]);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function formattedDate(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->timestamp)->format('Y-m-d H:i'),
        );
    }
}
