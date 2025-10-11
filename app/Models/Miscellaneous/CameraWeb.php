<?php

namespace App\Models\Miscellaneous;

use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function likes(): HasMany
    {
        return $this->hasMany(CameraLike::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(CameraView::class);
    }

    public function formattedDate(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->timestamp)->format('Y-m-d H:i')
        );
    }
}
