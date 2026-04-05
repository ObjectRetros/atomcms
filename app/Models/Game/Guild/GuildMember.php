<?php

namespace App\Models\Game\Guild;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $guild_id
 * @property int $user_id
 * @property int $level_id
 * @property int $member_since
 * @property-read Collection<int, Guild> $guilds
 * @property-read int|null $guilds_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember whereMemberSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuildMember whereUserId($value)
 *
 * @mixin \Eloquent
 */
class GuildMember extends Model
{
    protected $table = 'guilds_members';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function guilds(): HasMany
    {
        return $this->hasMany(Guild::class);
    }
}
