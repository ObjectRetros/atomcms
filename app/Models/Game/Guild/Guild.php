<?php

namespace App\Models\Game\Guild;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $description
 * @property int $room_id
 * @property int $state
 * @property string $rights
 * @property int $color_one
 * @property int $color_two
 * @property string $badge
 * @property int $date_created
 * @property string $forum
 * @property string $read_forum
 * @property string $post_messages
 * @property string $post_threads
 * @property string $mod_forum
 * @property-read Collection<int, GuildMember> $members
 * @property-read int|null $members_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereColorOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereColorTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereForum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereModForum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild wherePostMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild wherePostThreads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereReadForum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guild whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Guild extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function members(): HasMany
    {
        return $this->hasMany(GuildMember::class);
    }
}
