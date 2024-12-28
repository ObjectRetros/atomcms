<?php

namespace Database\Seeders;

use App\Models\HousekeepingPermission;
use Illuminate\Database\Seeder;

class HousekeepingPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'permission' => 'can_login',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to login to the housekeeping',
            ],
            [
                'permission' => 'edit_user',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to edit a user',
            ],
            [
                'permission' => 'reset_user_password',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to reset the password for a user',
            ],
            [
                'permission' => 'delete_user',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to delete a user',
            ],
            [
                'permission' => 'write_article',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to write an article',
            ],
            [
                'permission' => 'edit_article',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to edit an article',
            ],
            [
                'permission' => 'delete_article',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to delete an article',
            ],
            [
                'permission' => 'manage_wordfilter',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage the wordfilter',
            ],
            [
                'permission' => 'manage_bans',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage the bans',
            ],
            [
                'permission' => 'manage_room_chatlogs',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to read room chatlogs',
            ],
            [
                'permission' => 'manage_private_chatlogs',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to read private chatlogs',
            ],
            [
                'permission' => 'manage_catalog_pages',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage catalog pages',
            ],
            [
                'permission' => 'delete_catalog_pages',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to delete catalog pages',
            ],
            [
                'permission' => 'manage_emulator_settings',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage emulator settings',
            ],
            [
                'permission' => 'manage_emulator_texts',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage emulator texts',
            ],
            [
                'permission' => 'manage_website_settings',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage website settings',
            ],
            [
                'permission' => 'manage_website_blacklists',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage website blacklists',
            ],
            [
                'permission' => 'manage_website_whitelists',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage website whitelists',
            ],
            [
                'permission' => 'view_activity_logs',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to view activity logs',
            ],
            [
                'permission' => 'manage_staff_applications',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage staff applications',
            ],
            [
                'permission' => 'delete_website_settings',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to delete website settings',
            ],
            [
                'permission' => 'manage_permissions',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage hotel rank permissions',
            ],
            [
                'permission' => 'delete_permissions',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to delete hotel rank permissions',
            ],
            [
                'permission' => 'manage_article_tags',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage article tags',
            ],
            [
                'permission' => 'manage_teams',
                'min_rank' => 6,
                'description' => 'The minimum rank required before being able to manage teams',
            ],
            [
                'permission' => 'manage_achievements',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage achievements',
            ],
            [
                'permission' => 'manage_commandlogs',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage command logs',
            ],
            [
                'permission' => 'manage_camera_web',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage published camera photos',
            ],
            [
                'permission' => 'manage_housekeeping_permissions',
                'min_rank' => 7,
                'description' => 'The minimum rank required before being able to manage housekeeping permissions',
            ],
        ];

        foreach ($permissions as $permission) {
            HousekeepingPermission::query()->firstOrCreate(['permission' => $permission['permission']], [
                'permission' => $permission['permission'],
                'min_rank' => $permission['min_rank'],
                'description' => $permission['description'],
            ]);
        }
    }
}
