<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'theme',
                'value' => 'atom',
                'comment' => 'Specifies the theme of the CMS',
            ],
            [
                'key' => 'hotel_name',
                'value' => 'Habbo',
                'comment' => 'Specifies the name of the hotel',
            ],
            [
                'key' => 'rcon_ip',
                'value' => '127.0.0.1',
                'comment' => 'Specifies the RCON IP used for the CMS to perform RCON commands',
            ],
            [
                'key' => 'rcon_port',
                'value' => '3001',
                'comment' => 'Specifies the RCON port used for the CMS to perform RCON commands',
            ],
            [
                'key' => 'start_motto',
                'value' => sprintf('I love %s hotel', setting('hotel_name')),
                'comment' => 'Specifies the start motto the user gets assigned upon registration',
            ],
            [
                'key' => 'start_credits',
                'value' => '5000',
                'comment' => 'Specifies the amount of start credits upon registration',
            ],
            [
                'key' => 'start_duckets',
                'value' => '5000',
                'comment' => 'Specifies the amount of start duckets upon registration',
            ],
            [
                'key' => 'start_diamonds',
                'value' => '100',
                'comment' => 'Specifies the amount of start diamonds upon registration',
            ],
            [
                'key' => 'start_points',
                'value' => '0',
                'comment' => 'Specifies the amount of start points upon registration',
            ],
            [
                'key' => 'avatar_imager',
                'value' => 'https://www.habbo.com/habbo-imaging/avatarimage?figure=',
                'comment' => 'The base url for the imager used to render avatars on the CMS',
            ],
            [
                'key' => 'start_look',
                'value' => 'fa-201407-1324.hr-828-1035.ch-3001-1261-1408.sh-3068-92-1408.cp-9032-1308.lg-270-1281.hd-209-3',
                'comment' => 'Specifies the amount of start outfit upon registration',
            ],
            [
                'key' => 'referrals_needed',
                'value' => '5',
                'comment' => 'Specifies the amount of referrals needed before being able to claim the reward',
            ],
            [
                'key' => 'referral_reward_amount',
                'value' => '30',
                'comment' => 'Specifies the reward amount when a user claims a reward',
            ],
            [
                'key' => 'discord_invitation_link',
                'value' => 'https://discord.gg/rX3aShUHdg',
                'comment' => 'The link used to invite people to your Discord server',
            ],
            [
                'key' => 'discord_widget_id',
                'value' => '1008150166521524264',
                'comment' => 'The Discord widget ID you want to show on the CMS',
            ],
            [
                'key' => 'min_staff_rank',
                'value' => '4',
                'comment' => 'The minimum rank before being considered a staff member',
            ],
            [
                'key' => 'badges_path',
                'value' => '/client/flash/swfs/c_images/album1584',
                'comment' => 'The path to the badges folder',
            ],
            [
                'key' => 'group_badge_path',
                'value' => '/ms-swf/c_images/Badgeparts/generated',
                'comment' => 'The path that contains all the generated group badges',
            ],
            [
                'key' => 'maintenance_enabled',
                'value' => '0',
                'comment' => 'Determines if maintenance is enabled or not',
            ],
            [
                'key' => 'min_maintenance_login_rank',
                'value' => '5',
                'comment' => 'Determines the minimum rank before being able to login while maintenance is activated',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Atom is currently undergoing maintenance. We will be back shortly!',
                'comment' => 'The maintenance message displayed to users while maintenance is activated',
            ],
            [
                'key' => 'username_regex',
                'value' => '/^[a-zA-Z0-9_.-]+$/u',
                'comment' => 'The regex used to validate username input fields',
            ],
            [
                'key' => 'min_housekeeping_rank',
                'value' => '6',
                'comment' => 'The minimum rank required to see the housekeeping button',
            ],
            [
                'key' => 'housekeeping_url',
                'value' => 'https://hk.example.com',
                'comment' => 'The subdomain which the HK is hosted on',
            ],
            [
                'key' => 'max_accounts_per_ip',
                'value' => '2',
                'comment' => 'The maximum allowed accounts registered per IP address',
            ],
            [
                'key' => 'google_recaptcha_enabled',
                'value' => '0',
                'comment' => 'Toggles whether google recaptcha is enabled or not',
            ],
            [
                'key' => 'vpn_block_enabled',
                'value' => '0',
                'comment' => 'Toggles whether the VPN blocker is enabled or not',
            ],
            [
                'key' => 'ipdata_api_key',
                'value' => 'ADD-API-KEY-HERE',
                'comment' => 'The API key needed from ipdata.co to block VPNs',
            ],
            [
                'key' => 'cms_logo',
                'value' => '/assets/images/kasja_atomlogo.png',
                'comment' => 'Default logo for the cms',
            ],
            [
                'key' => 'cms_header',
                'value' => '/assets/images/kasja_mepage_header.png',
                'comment' => 'Default header for the me page',
            ],
            [
                'key' => 'cms_me_backdrop',
                'value' => '/assets/images/kasja_mepage_image.png',
                'comment' => 'Default backdrop header for the me page',
            ],
            [
                'key' => 'room_thumbnail_path',
                'value' => '/ms-swf/c_images/rooms',
                'comment' => 'Path to room thumbnails',
            ],
            [
                'key' => 'hotel_home_room',
                'value' => '0',
                'comment' => 'The homeroom every new users will be assigned to',
            ],
            [
<<<<<<< HEAD
                'key' => 'paypal_sandbox_client_id',
                'value' => '',
                'comment' => 'The sandbox client ID provided by paypal (Only use this if your paypal is in sandbox)',
            ],
            [
                'key' => 'paypal_client_secret',
                'value' => '',
                'comment' => 'The sandbox secret client ID provided by paypal (Only use this if your paypal is in sandbox)',
            ],
            [
                'key' => 'paypal_live_client_id',
                'value' => '',
                'comment' => 'The live client ID provided by paypal (Use this if your paypal account is set to live)',
            ],
            [
                'key' => 'paypal_live_client_secret',
                'value' => '',
                'comment' => 'The live secret client ID provided by paypal (Use this if your paypal account is set to live)',
            ],
            [
                'key' => 'paypal_locale',
                'value' => 'en-US',
                'comment' => 'The locale used for Paypal (Only change this if you are absolutely certain on what you are doing',
            ],
            [
                'key' => 'paypal_currency',
                'value' => 'USD',
                'comment' => 'The currency type used to charge the user (Only change this if you are absolutely certain on what you are doing',
=======
                'key' => 'cms_color_mode',
                'value' => 'light',
                'comment' => 'Determines the color mode of the CMS (light = normal, dark = dark mode)',
            ],
            [
                'key' => 'force_staff_2fa',
                'value' => '0',
                'comment' => 'If set to 1 every staff will be forced to apply 2FA before being able to visit any other page',
            ],
            [
                'key' => 'website_wordfilter_enabled',
                'value' => '1',
                'comment' => 'Determines whether the wordfilter for CMS will be enabled or not',
            ],
            [
                'key' => 'requires_beta_code',
                'value' => '0',
                'comment' => 'Determines whether users need a beta code to register or not',
            ],
            [
                'key' => 'disable_registration',
                'value' => '0',
                'comment' => 'Determines whether registration is enabled or not',
            ],
            [
                'key' => 'give_hc_on_register',
                'value' => '0',
                'comment' => 'Determines whether the registered user will receive a HC on registration',
            ],
            [
                'key' => 'hc_on_register_duration',
                'value' => '315569260',
                'comment' => 'Specifies the amount of time a user receives their HC subscription for (The default amount is 10 years)',
            ],
            [
                'key' => 'max_comment_per_article',
                'value' => '2',
                'comment' => 'Specifies the amount of times a user can comment per article',
>>>>>>> main
            ],
        ];

        foreach ($settings as $setting) {
            WebsiteSetting::firstOrCreate(['key' => $setting['key']], [
                'key' => $setting['key'],
                'value' => $setting['value'],
                'comment' => $setting['comment'],
            ]);
        }
    }
}
