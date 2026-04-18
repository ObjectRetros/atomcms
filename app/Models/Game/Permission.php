<?php

namespace App\Models\Game;

use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\Compositions\HasBadge;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $rank_name
 * @property int $hidden_rank
 * @property string $badge
 * @property string $job_description
 * @property string $staff_color
 * @property string $staff_background
 * @property int $level
 * @property int $room_effect
 * @property string $log_commands
 * @property string $prefix
 * @property string $prefix_color
 * @property string $cmd_about
 * @property string $cmd_alert
 * @property string $cmd_allow_trading
 * @property string $cmd_badge
 * @property string $cmd_ban
 * @property string $cmd_blockalert
 * @property string $cmd_bots
 * @property string $cmd_bundle
 * @property string $cmd_calendar
 * @property string $cmd_changename
 * @property string $cmd_chatcolor
 * @property string $cmd_commands
 * @property string $cmd_connect_camera
 * @property string $cmd_control
 * @property string $cmd_coords
 * @property string $cmd_credits
 * @property string|null $cmd_subscription
 * @property string $cmd_danceall
 * @property string $cmd_diagonal
 * @property string $cmd_disconnect
 * @property string $cmd_duckets
 * @property string $cmd_ejectall
 * @property string $cmd_empty
 * @property string $cmd_empty_bots
 * @property string $cmd_empty_pets
 * @property string $cmd_enable
 * @property string $cmd_event
 * @property string $cmd_faceless
 * @property string $cmd_fastwalk
 * @property string $cmd_filterword
 * @property string $cmd_freeze
 * @property string $cmd_freeze_bots
 * @property string $cmd_gift
 * @property string $cmd_give_rank
 * @property string $cmd_ha
 * @property string $acc_can_stalk
 * @property string $cmd_hal
 * @property string $cmd_invisible
 * @property string $cmd_ip_ban
 * @property string $cmd_machine_ban
 * @property string $cmd_hand_item
 * @property string $cmd_happyhour
 * @property string $cmd_hidewired
 * @property string $cmd_kickall
 * @property string $cmd_softkick
 * @property string $cmd_massbadge
 * @property string $cmd_roombadge
 * @property string $cmd_masscredits
 * @property string $cmd_massduckets
 * @property string $cmd_massgift
 * @property string $cmd_masspoints
 * @property string $cmd_moonwalk
 * @property string $cmd_mimic
 * @property string $cmd_multi
 * @property string $cmd_mute
 * @property string $cmd_pet_info
 * @property string $cmd_pickall
 * @property string $cmd_plugins
 * @property string $cmd_points
 * @property string $cmd_promote_offer
 * @property string $cmd_pull
 * @property string $cmd_push
 * @property string $cmd_redeem
 * @property string $cmd_reload_room
 * @property string $cmd_roomalert
 * @property string $cmd_roomcredits
 * @property string $cmd_roomeffect
 * @property string $cmd_roomgift
 * @property string $cmd_roomitem
 * @property string $cmd_roommute
 * @property string $cmd_roompixels
 * @property string $cmd_roompoints
 * @property string $cmd_say
 * @property string $cmd_say_all
 * @property string $cmd_setmax
 * @property string $cmd_set_poll
 * @property string $cmd_setpublic
 * @property string $cmd_setspeed
 * @property string $cmd_shout
 * @property string $cmd_shout_all
 * @property string $cmd_shutdown
 * @property string $cmd_sitdown
 * @property string $cmd_staffalert
 * @property string $cmd_staffonline
 * @property string $cmd_summon
 * @property string $cmd_summonrank
 * @property string $cmd_super_ban
 * @property string $cmd_stalk
 * @property string $cmd_superpull
 * @property string $cmd_take_badge
 * @property string $cmd_talk
 * @property string $cmd_teleport
 * @property string $cmd_trash
 * @property string $cmd_transform
 * @property string $cmd_unban
 * @property string $cmd_unload
 * @property string $cmd_unmute
 * @property string $cmd_update_achievements
 * @property string $cmd_update_bots
 * @property string $cmd_update_catalogue
 * @property string $cmd_update_config
 * @property string $cmd_update_guildparts
 * @property string $cmd_update_hotel_view
 * @property string $cmd_update_items
 * @property string $cmd_update_navigator
 * @property string $cmd_update_permissions
 * @property string $cmd_update_pet_data
 * @property string $cmd_update_plugins
 * @property string $cmd_update_polls
 * @property string $cmd_update_texts
 * @property string $cmd_update_wordfilter
 * @property string $cmd_userinfo
 * @property string $cmd_word_quiz
 * @property string $cmd_warp
 * @property string $acc_anychatcolor
 * @property string $acc_anyroomowner
 * @property string $acc_empty_others
 * @property string $acc_enable_others
 * @property string $acc_see_whispers
 * @property string $acc_see_tentchat
 * @property string $acc_superwired
 * @property string $acc_supporttool
 * @property string $acc_unkickable
 * @property string $acc_guildgate
 * @property string $acc_moverotate
 * @property string $acc_placefurni
 * @property string $acc_unlimited_bots Overrides the bot restriction to the inventory and room.
 * @property string $acc_unlimited_pets Overrides the pet restriction to the inventory and room.
 * @property string $acc_hide_ip
 * @property string $acc_hide_mail
 * @property string $acc_not_mimiced
 * @property string $acc_chat_no_flood
 * @property string $acc_staff_chat
 * @property string $acc_staff_pick
 * @property string $acc_enteranyroom
 * @property string $acc_fullrooms
 * @property string $acc_infinite_credits
 * @property string $acc_infinite_pixels
 * @property string $acc_infinite_points
 * @property string $acc_ambassador
 * @property string $acc_debug
 * @property string $acc_chat_no_limit People with this permission node are always heard and can see all chat in the room regarding of maximum hearing distance in the room settings (In game)
 * @property string $acc_chat_no_filter
 * @property string $acc_nomute
 * @property string $acc_guild_admin
 * @property string $acc_catalog_ids
 * @property string $acc_modtool_ticket_q
 * @property string $acc_modtool_user_logs
 * @property string $acc_modtool_user_alert
 * @property string $acc_modtool_user_kick
 * @property string $acc_modtool_user_ban
 * @property string $acc_modtool_room_info
 * @property string $acc_modtool_room_logs
 * @property string $acc_trade_anywhere
 * @property string $acc_update_notifications
 * @property string $acc_helper_use_guide_tool
 * @property string $acc_helper_give_guide_tours
 * @property string $acc_helper_judge_chat_reviews
 * @property string $acc_floorplan_editor
 * @property string $acc_camera
 * @property string $acc_ads_background
 * @property string $cmd_wordquiz
 * @property string $acc_room_staff_tags
 * @property string $acc_infinite_friends
 * @property string $acc_unignorable
 * @property string $acc_mimic_unredeemed
 * @property string $cmd_update_youtube_playlists
 * @property string $cmd_add_youtube_playlist
 * @property int|null $auto_credits_amount
 * @property int|null $auto_pixels_amount
 * @property int|null $auto_gotw_amount
 * @property int|null $auto_points_amount
 * @property string $acc_mention
 * @property string $cmd_setstate
 * @property string $cmd_buildheight
 * @property string $cmd_setrotation
 * @property string $cmd_sellroom
 * @property string $cmd_buyroom
 * @property string $cmd_pay
 * @property string $cmd_kill
 * @property string $cmd_hoverboard
 * @property string $cmd_kiss
 * @property string $cmd_hug
 * @property string $cmd_welcome
 * @property string $cmd_disable_effects
 * @property string $cmd_brb
 * @property string $cmd_nuke
 * @property string $cmd_slime
 * @property string $cmd_explain
 * @property string $cmd_closedice
 * @property string $acc_closedice_room
 * @property string $cmd_set
 * @property string $cmd_furnidata
 * @property string $kiss_cmd
 * @property string|null $acc_calendar_force
 * @property string $cmd_update_calendar
 * @property string $cmd_update_chat_bubbles
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccAdsBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccAmbassador($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccAnychatcolor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccAnyroomowner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccCalendarForce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccCamera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccCanStalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccCatalogIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccChatNoFilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccChatNoFlood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccChatNoLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccClosediceRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccDebug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccEmptyOthers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccEnableOthers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccEnteranyroom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccFloorplanEditor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccFullrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccGuildAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccGuildgate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccHelperGiveGuideTours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccHelperJudgeChatReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccHelperUseGuideTool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccHideIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccHideMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccInfiniteCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccInfiniteFriends($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccInfinitePixels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccInfinitePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccMention($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccMimicUnredeemed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolRoomInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolRoomLogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolTicketQ($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolUserAlert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolUserBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolUserKick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccModtoolUserLogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccMoverotate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccNomute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccNotMimiced($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccPlacefurni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccRoomStaffTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccSeeTentchat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccSeeWhispers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccStaffChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccStaffPick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccSuperwired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccSupporttool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccTradeAnywhere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccUnignorable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccUnkickable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccUnlimitedBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccUnlimitedPets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAccUpdateNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAutoCreditsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAutoGotwAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAutoPixelsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAutoPointsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdAddYoutubePlaylist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdAlert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdAllowTrading($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBlockalert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBrb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBuildheight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBundle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdBuyroom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdCalendar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdChangename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdChatcolor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdClosedice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdCommands($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdConnectCamera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdControl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdCoords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdDanceall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdDiagonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdDisableEffects($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdDisconnect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdDuckets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEjectall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEmpty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEmptyBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEmptyPets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdExplain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFaceless($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFastwalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFilterword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFreezeBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdFurnidata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdGiveRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHandItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHappyhour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHidewired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHoverboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdHug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdInvisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdIpBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdKickall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdKill($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdKiss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMachineBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMassbadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMasscredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMassduckets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMassgift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMasspoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMimic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMoonwalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMulti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdMute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdNuke($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPetInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPickall($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPlugins($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPromoteOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPull($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRedeem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdReloadRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoomalert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoombadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoomcredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoomeffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoomgift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoomitem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoommute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoompixels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdRoompoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSayAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSellroom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetPoll($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetmax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetpublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetrotation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetspeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSetstate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdShout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdShoutAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdShutdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSitdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSlime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSoftkick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdStaffalert($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdStaffonline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdStalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSummon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSummonrank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSuperBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdSuperpull($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdTakeBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdTalk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdTeleport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdTransform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdTrash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUnban($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUnload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUnmute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateAchievements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateBots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateCalendar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateCatalogue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateChatBubbles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateGuildparts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateHotelView($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateNavigator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdatePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdatePetData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdatePlugins($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdatePolls($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateTexts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateWordfilter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUpdateYoutubePlaylists($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdUserinfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdWarp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdWelcome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCmdWordQuiz($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereHiddenRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereJobDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereKissCmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereLogCommands($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission wherePrefixColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereRankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereRoomEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereStaffBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereStaffColor($value)
 *
 * @property-read Collection<int, WebsiteStaffApplications> $staffApplications
 * @property-read int|null $staff_applications_count
 *
 * @mixin \Eloquent
 */
class Permission extends Model implements HasBadge
{
    protected $table = 'permissions';

    public $timestamps = false;

    protected $guarded = ['id', 'rank_name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rank', 'id');
    }

    public function staffApplications(): HasMany
    {
        return $this->hasMany(WebsiteStaffApplications::class, 'rank_id');
    }

    public function getBadgePath(): string
    {
        return sprintf('%s%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return $this->badge;
    }
}
