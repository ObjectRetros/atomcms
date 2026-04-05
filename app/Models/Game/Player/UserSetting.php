<?php

namespace App\Models\Game\Player;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id WARNING: DONT HAVE YOUR CMS INSERT ANYTHING IN HERE. THE EMULATOR DOES THIS FOR YOU!
 * @property int $credits
 * @property int $achievement_score
 * @property int $daily_respect_points
 * @property int $daily_pet_respect_points
 * @property int $respects_given
 * @property int $respects_received
 * @property int $guild_id
 * @property string $can_change_name
 * @property string $can_trade
 * @property string $is_citizen
 * @property int $citizen_level
 * @property int $helper_level
 * @property int $tradelock_amount
 * @property int $cfh_send Amount of CFHs been send. Not include abusive.
 * @property int $cfh_abusive Amount of abusive CFHs have been send.
 * @property int $cfh_warnings Amount of warnings a user has received.
 * @property int $cfh_bans Amount of bans a user has received.
 * @property string $block_following
 * @property string $block_friendrequests
 * @property string $block_roominvites
 * @property int $volume_system
 * @property int $volume_furni
 * @property int $volume_trax
 * @property string $old_chat
 * @property string $block_camera_follow
 * @property int $chat_color
 * @property int $home_room
 * @property int $online_time Total online time in seconds.
 * @property string $tags
 * @property int $club_expire_timestamp
 * @property int $login_streak
 * @property int $rent_space_id
 * @property int $rent_space_endtime
 * @property int $hof_points
 * @property string $block_alerts
 * @property int $talent_track_citizenship_level
 * @property int $talent_track_helpers_level
 * @property string $ignore_bots
 * @property string $ignore_pets
 * @property string $nux
 * @property int $mute_end_timestamp
 * @property string $allow_name_change
 * @property string $perk_trade Defines if a player has obtained the perk TRADE. When hotel.trading.requires.perk is set to 1, this perk is required in order to trade. Perk is obtained from the talen track.
 * @property int|null $forums_post_count
 * @property int $ui_flags
 * @property int $has_gotten_default_saved_searches
 * @property int|null $hc_gifts_claimed
 * @property int|null $last_hc_payday
 * @property int|null $max_rooms
 * @property int|null $max_friends
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereAchievementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereAllowNameChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockAlerts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockCameraFollow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockFollowing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockFriendrequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereBlockRoominvites($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCanChangeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCanTrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCfhAbusive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCfhBans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCfhSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCfhWarnings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereChatColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCitizenLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereClubExpireTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereDailyPetRespectPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereDailyRespectPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereForumsPostCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereHasGottenDefaultSavedSearches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereHcGiftsClaimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereHelperLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereHofPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereHomeRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereIgnoreBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereIgnorePets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereIsCitizen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereLastHcPayday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereLoginStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereMaxFriends($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereMaxRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereMuteEndTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereNux($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereOldChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereOnlineTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting wherePerkTrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereRentSpaceEndtime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereRentSpaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereRespectsGiven($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereRespectsReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTalentTrackCitizenshipLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTalentTrackHelpersLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereTradelockAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereUiFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereVolumeFurni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereVolumeSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSetting whereVolumeTrax($value)
 *
 * @mixin \Eloquent
 */
class UserSetting extends Model
{
    protected $table = 'users_settings';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
