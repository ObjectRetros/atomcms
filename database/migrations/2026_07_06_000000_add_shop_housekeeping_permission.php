<?php

use App\Models\WebsiteHousekeepingPermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Gates the shop package, item and category Filament resources; without it
     * the panel's strict authorization denies them for everyone.
     *
     * @var array{permission: string, min_rank: int, description: string}
     */
    private array $permission = [
        'permission' => 'manage_shop',
        'min_rank' => 7,
        'description' => 'The minimum rank required before being able to manage shop packages, items and categories',
    ];

    public function up(): void
    {
        WebsiteHousekeepingPermission::query()->firstOrCreate(
            ['permission' => $this->permission['permission']],
            $this->permission,
        );
    }

    public function down(): void
    {
        WebsiteHousekeepingPermission::query()
            ->where('permission', $this->permission['permission'])
            ->delete();
    }
};
