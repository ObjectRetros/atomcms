<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_from_id
 * @property int $user_to_id
 * @property string $message
 * @property int $timestamp
 * @property-read User|null $receiver
 * @property-read User|null $sender
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate whereUserFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChatlogPrivate whereUserToId($value)
 *
 * @mixin \Eloquent
 */
class ChatlogPrivate extends Model
{
    use HasFactory;

    protected $table = 'chatlogs_private';

    protected $guarded = [];

    public $timestamps = false;

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_from_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to_id');
    }
}
