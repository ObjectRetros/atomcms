<?php

namespace App\Models;

use App\Models\Game\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory;

    protected $table = 'chatlogs_room';

    protected $guarded = [];

    public $timestamps = false;

    protected $primaryKey = 'timestamp';

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_from_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }
}
