<?php

namespace App\Models\Plus;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A room chat line on the Plus EMU schema.
 *
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property int $timestamp Unix timestamp
 * @property string $message
 */
class PlusChatlog extends Model
{
    protected $table = 'chatlogs';

    protected $fillable = [
        'user_id',
        'room_id',
        'timestamp',
        'message',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
