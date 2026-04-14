<?php

namespace App\Models;

use App\Models\Game\Furniture\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $owner_name
 * @property string $name
 * @property string $description
 * @property string $model
 * @property string $password
 * @property string $state
 * @property int $users
 * @property int $users_max
 * @property int $guild_id
 * @property int $category
 * @property int $score
 * @property string $paper_floor
 * @property string $paper_wall
 * @property string $paper_landscape
 * @property int $thickness_wall
 * @property int $wall_height
 * @property int $thickness_floor
 * @property string $moodlight_data
 * @property string $tags
 * @property string $is_public
 * @property string $is_staff_picked
 * @property string $allow_other_pets
 * @property string $allow_other_pets_eat
 * @property string $allow_walkthrough
 * @property string $allow_hidewall
 * @property int $chat_mode
 * @property int $chat_weight
 * @property int $chat_speed
 * @property int $chat_hearing_distance
 * @property int $chat_protection
 * @property string $override_model
 * @property int $who_can_mute
 * @property int $who_can_kick
 * @property int $who_can_ban
 * @property int $poll_id
 * @property int $roller_speed
 * @property string $promoted
 * @property int $trade_mode
 * @property string $move_diagonally
 * @property string $jukebox_active
 * @property string $hidewired
 * @property string $is_forsale
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereAllowHidewall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereAllowOtherPets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereAllowOtherPetsEat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereAllowWalkthrough($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChatHearingDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChatMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChatProtection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChatSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChatWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereHidewired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereIsForsale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereIsStaffPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereJukeboxActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereMoodlightData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereMoveDiagonally($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereOverrideModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePaperFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePaperLandscape($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePaperWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePromoted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRollerSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereThicknessFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereThicknessWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereTradeMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUsersMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereWallHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereWhoCanBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereWhoCanKick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereWhoCanMute($value)
 *
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 *
 * @mixin \Eloquent
 */
class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'room_id');
    }

    public function replicateForUser(User $user): self
    {
        $replicatedRoom = $this->replicate();

        $replicatedRoom->owner_id = $user->id;
        $replicatedRoom->owner_name = $user->username;
        $replicatedRoom->score = 0;
        $replicatedRoom->guild_id = 0;
        $replicatedRoom->is_public = '0';
        $replicatedRoom->is_staff_picked = '0';

        $replicatedRoom->save();

        $items = [];

        foreach ($this->items as $item) {
            $replicatedItem = $item->replicate();

            $replicatedItem->user_id = $user->id;
            $replicatedItem->room_id = $replicatedRoom->id;

            $items[] = $replicatedItem;
        }

        $replicatedRoom->items()->saveMany($items);

        return $replicatedRoom;
    }
}
