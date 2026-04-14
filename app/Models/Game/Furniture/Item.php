<?php

namespace App\Models\Game\Furniture;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property int|null $item_id
 * @property string $wall_pos
 * @property int $x
 * @property int $y
 * @property float $z
 * @property int $rot
 * @property string $extra_data
 * @property string $wired_data
 * @property string $limited_data
 * @property int $guild_id
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereExtraData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLimitedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereWallPos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereWiredData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereZ($value)
 *
 * @mixin \Eloquent
 */
class Item extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
