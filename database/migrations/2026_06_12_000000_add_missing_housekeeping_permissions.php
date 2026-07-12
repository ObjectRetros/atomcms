<?php

use App\Models\WebsiteHousekeepingPermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Permissions gating Filament resources that previously had no policy and
     * were therefore reachable by any housekeeping user.
     *
     * @var list<array{permission: string, min_rank: int, description: string}>
     */
    private array $permissions = [
        [
            'permission' => 'manage_home_items',
            'min_rank' => 7,
            'description' => 'The minimum rank required before being able to manage user home items and categories',
        ],
        [
            'permission' => 'manage_badges',
            'min_rank' => 6,
            'description' => 'The minimum rank required before being able to manage badges, badge uploads and drawn badges',
        ],
        [
            'permission' => 'manage_website_ads',
            'min_rank' => 6,
            'description' => 'The minimum rank required before being able to manage website advertisements',
        ],
    ];

    public function up(): void
    {
        foreach ($this->permissions as $permission) {
            WebsiteHousekeepingPermission::query()->firstOrCreate(
                ['permission' => $permission['permission']],
                $permission,
            );
        }
    }

    public function down(): void
    {
        WebsiteHousekeepingPermission::query()
            ->whereIn('permission', array_column($this->permissions, 'permission'))
            ->delete();
    }
};
