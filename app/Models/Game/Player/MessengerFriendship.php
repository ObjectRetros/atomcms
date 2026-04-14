<?php

namespace App\Models\Game\Player;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_one_id
 * @property int $user_two_id
 * @property int $relation
 * @property int $friends_since
 * @property int $category
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereFriendsSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereRelation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereUserOneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessengerFriendship whereUserTwoId($value)
 *
 * @mixin \Eloquent
 */
class MessengerFriendship extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id', 'id');
    }
}
