<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $room_id
 * @property int $user_from_id
 * @property int $user_to_id
 * @property string $message
 * @property int $timestamp
 * @property-read User|null $receiver
 * @property-read Room|null $room
 * @property-read User|null $sender
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom whereUserFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogRoom whereUserToId($value)
 *
 * @mixin \Eloquent
 */
class ChatlogRoom extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $table = 'chatlogs_room';

    protected $guarded = [];

    public $timestamps = false;

    // chatlogs_room has no primary key, so keyed operations are unsupported.
    protected $primaryKey = null;

    public $incrementing = false;

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_from_id');
    }

    /** @return BelongsTo<User, $this> */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }
}
