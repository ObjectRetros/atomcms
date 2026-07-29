<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Emulator\Contracts\RankRepository;
use App\Emulator\EmulatorManager;
use App\Models\Miscellaneous\WebsiteInstallation;
use App\Models\Miscellaneous\WebsiteSetting;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\WebsiteHousekeepingPermission;
use App\Observers\CommunityCacheObserver;
use App\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\AdaTestCase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(AdaTestCase::class, RefreshDatabase::class)->in('Ada');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function installHotel(): void
{
    WebsiteInstallation::query()->firstOrCreate(['installation_key' => 'key'], ['completed' => true]);

    setSetting('max_accounts_per_ip', '10');
}

function setSetting(string $key, string $value): void
{
    WebsiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'comment' => '']);

    app(SettingsService::class)->refresh();
}

function makePackage(array $attributes = []): WebsiteShopPackage
{
    $package = WebsiteShopPackage::create([
        'name' => 'Starter Bundle',
        'price' => 500, // $5.00
        ...$attributes,
    ]);

    $credits = WebsiteShopItem::create([
        'name' => '100 Credits',
        'type' => 'currency',
        'type_value' => 'credits:100',
        'is_active' => true,
    ]);

    $package->items()->attach($credits->id, ['quantity' => 2]);

    return $package;
}

function grantHousekeepingPermission(string $permission, int $minRank): void
{
    WebsiteHousekeepingPermission::query()->create([
        'permission' => $permission,
        'min_rank' => $minRank,
        'description' => "Testing {$permission}",
    ]);
}

/**
 * A console command that never prompts, for exercising installers without a
 * real console run.
 */
function silentCommand(): Command
{
    return new class extends Command
    {
        public function option($key = null): mixed
        {
            return $key === 'no-interaction';
        }
    };
}

/**
 * Ada declares real foreign keys, so a room needs a layout and an inventory
 * item needs a definition. These build the minimum Ada accepts.
 */
function makeAdaFurnitureItem(string $name = 'Ada chair'): int
{
    return (int) DB::table('furniture_items')->insertGetId([
        'name' => $name, 'asset_name' => 'chair', 'type' => 's', 'asset_id' => 1,
        'tile_span_x' => 1, 'tile_span_y' => 1, 'stack_height' => 1, 'can_stack' => true,
        'can_walk' => false, 'can_sit' => true, 'can_lay' => false, 'can_recycle' => true,
        'can_trade' => true, 'can_marketplace_sell' => true, 'can_inventory_stack' => true,
        'can_gift' => true, 'interaction_modes' => 0,
    ]);
}

function makeAdaRoom(int $ownerId, ?int $accessType = null): int
{
    $layoutId = (int) DB::table('room_layouts')->insertGetId([
        'name' => 'model_a', 'heightmap' => 'xxx', 'door_x' => 1, 'door_y' => 1,
        'door_direction' => 2, 'requires_club_membership' => false,
    ]);

    $roomId = (int) DB::table('rooms')->insertGetId([
        'owner_id' => $ownerId, 'name' => 'Ada room', 'description' => 'EF room',
        'layout_id' => $layoutId, 'max_users_allowed' => 10, 'is_muted' => false,
        'created_at' => now(),
    ]);

    if ($accessType !== null) {
        DB::table('room_settings')->insert([
            'room_id' => $roomId, 'access_type' => $accessType, 'walk_diagonal' => true,
            'who_can_mute' => 0, 'who_can_kick' => 0, 'who_can_ban' => 0, 'allow_pets' => true,
            'can_pets_eat' => true, 'hide_walls' => false, 'wall_thickness' => 0,
            'floor_thickness' => 0, 'can_users_overlap' => false, 'trade_option' => 0,
        ]);
    }

    return $roomId;
}

/**
 * Point the container at the Ada driver, including the driver-specific
 * observers EventServiceProvider wires up for the active rank model.
 *
 * This swaps the bindings only - the database still holds whichever schema the
 * suite was booted against. Tests that read or write Ada's tables belong in
 * tests/Ada, which runs against a database Ada owns.
 */
function useAdaSchema(): void
{
    app(EmulatorManager::class)->select('ada');

    CommunityCacheObserver::observeRanks(app(RankRepository::class));
}
