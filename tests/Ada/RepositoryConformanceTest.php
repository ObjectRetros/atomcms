<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\BanRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\FurnitureRepository;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Contracts\RankRepository;
use App\Emulator\Contracts\RoomRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\OwnedBadge;
use App\Emulator\Data\RoomSummary;
use App\Emulator\Data\Stat;
use App\Emulator\EmulatorManager;
use App\Enums\CurrencyTypes;
use App\Models\Miscellaneous\WebsitePermission;
use App\Models\User;
use App\Models\WebsiteHousekeepingPermission;
use App\Services\HousekeepingPermissionsService;
use App\Services\PermissionsService;
use Database\Seeders\HousekeepingPermissionSeeder;
use Database\Seeders\WebsitePermissionSeeder;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The Arcturus half of these behaviours lives in tests/Feature/Emulator. Both
 * halves exist because the two emulators cannot share a database: they own
 * overlapping table names, so each is exercised against its own schema.
 */
beforeEach(function () {
    installHotel();
    setSetting('start_duckets', '0');
    setSetting('start_diamonds', '0');
    setSetting('start_points', '0');
    setSetting('give_hc_on_register', '0');
});

test('granted badges are listed by code and revoked individually', function () {
    $badges = app(BadgeRepository::class);
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Login1');
    $badges->grant($user, 'ACH_RoomEntry1');

    expect($badges->codes($user))->toEqualCanonicalizing(['ACH_Login1', 'ACH_RoomEntry1']);

    $badges->revoke($user, 'ACH_Login1');

    expect($badges->codes($user))->toBe(['ACH_RoomEntry1']);
});

test('granting an owned badge is a no-op without a unique constraint', function () {
    // Ada indexes player_id and badge_id separately and enforces no
    // uniqueness, so the driver has to guarantee this itself.
    $badges = app(BadgeRepository::class);
    $user = User::factory()->create();
    $other = User::factory()->create();

    $badges->grant($user, 'ACH_Once');
    $badges->grant($user, 'ACH_Once');
    $badges->grant($other, 'ACH_Once');

    expect($badges->codes($user))->toBe(['ACH_Once'])
        ->and(DB::table('player_badges')->where('player_id', $user->id)->count())->toBe(1)
        ->and(DB::table('badges')->where('code', 'ACH_Once')->count())->toBe(1)
        ->and($badges->relation($other)->count())->toBe(1);
});

test('badges paginate as normalised entries, newest first', function () {
    $badges = app(BadgeRepository::class);
    $user = User::factory()->create();

    $badges->grant($user, 'ACH_Older');
    $badges->grant($user, 'ACH_Newer');

    $page = $badges->paginate($user, 16, 'badges_page');

    expect($page->total())->toBe(2)
        ->and($page->items()[0])->toBeInstanceOf(OwnedBadge::class)
        ->and($page->items()[0]->badge_code)->toBe('ACH_Newer');
});

test('currencies are read, written and deducted against player_data', function () {
    $currencies = app(CurrencyRepository::class);
    $user = User::factory()->create();

    $currencies->give($user, CurrencyTypes::Credits, 100);
    $currencies->give($user, CurrencyTypes::Duckets, 50);
    $currencies->give($user, CurrencyTypes::Diamonds, 5);
    $currencies->give($user->fresh(), CurrencyTypes::Duckets, -20);

    expect($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(30)
        ->and($currencies->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(5)
        ->and($currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 40))->toBeFalse()
        ->and($currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 20))->toBeTrue()
        ->and($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(10)
        ->and($currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 0))->toBeTrue();
});

test('the currency leaderboard ranks richer players first', function () {
    $currencies = app(CurrencyRepository::class);
    $rich = User::factory()->create();
    $poor = User::factory()->create();

    $currencies->give($rich, CurrencyTypes::Duckets, 2_000);
    $currencies->give($poor, CurrencyTypes::Duckets, 1_000);

    expect($currencies->topBy(CurrencyTypes::Duckets, 2)->map(fn ($entry) => $entry->user->id)->all())
        ->toBe([$rich->id, $poor->id]);
});

test('inventory rows reference the base definition through furniture_item_id', function () {
    $furniture = app(FurnitureRepository::class);
    $user = User::factory()->create();
    $itemId = makeAdaFurnitureItem();

    $furniture->grant($user, $itemId, 3);

    expect(DB::table('player_furniture_items')
        ->where('player_id', $user->id)
        ->where('furniture_item_id', $itemId)
        ->count())->toBe(3)
        ->and((array) $furniture->holdings($itemId)->first())->toMatchArray([
            'user_id' => $user->id,
            'item_count' => 3,
        ]);
});

test('furniture definitions are counted from ada tables and never limited', function () {
    $itemId = makeAdaFurnitureItem();

    expect(app(FurnitureRepository::class)->definitionCount())->toBe(1)
        ->and(app(FurnitureRepository::class)->isLimitedEdition($itemId))->toBeFalse();
});

test('bans are resolved from the split ada tables', function () {
    $bans = app(BanRepository::class);
    $user = User::factory()->create();

    DB::table('banned_ip_addresses')->insert([
        'creator_id' => $user->id, 'ip_address' => '10.0.0.1', 'reason' => 'IP misuse',
        'created_at' => now(), 'expires_at' => null,
    ]);
    DB::table('player_bans')->insert([
        'creator_id' => $user->id, 'player_id' => $user->id, 'reason' => 'Account misuse',
        'created_at' => now(), 'expires_at' => now()->addHour(),
    ]);

    // A permanent Ada ban has no expiry at all. Reporting it as timestamp 0
    // would render on the banned page as having lapsed in 1970.
    expect($bans->activeIpBan('10.0.0.1')?->ban_reason)->toBe('IP misuse')
        ->and($bans->activeIpBan('10.0.0.1')?->ban_expire)->toBeNull()
        ->and($bans->activeIpBan('10.0.0.1')?->type)->toBe('ip')
        ->and($bans->activeIpBan('10.0.0.2'))->toBeNull()
        ->and($bans->activeAccountBan($user)?->type)->toBe('account')
        ->and($bans->activeAccountBan($user)?->ban_expire)->toBeGreaterThan(time());
});

test('expired ada bans are ignored', function () {
    $user = User::factory()->create();

    DB::table('player_bans')->insert([
        'creator_id' => $user->id, 'player_id' => $user->id, 'reason' => 'Old',
        'created_at' => now()->subDay(), 'expires_at' => now()->subSecond(),
    ]);

    expect(app(BanRepository::class)->activeAccountBan($user))->toBeNull();
});

test('the banned page renders a permanent ban as never expiring', function () {
    setSetting('theme', 'atom');
    $user = User::factory()->create();

    DB::table('player_bans')->insert([
        'creator_id' => $user->id, 'player_id' => $user->id, 'reason' => 'Permanent',
        'created_at' => now(), 'expires_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('banned.show'))
        ->assertOk()
        ->assertSee('Permanent')
        ->assertSee(__('Never'))
        ->assertDontSee('1970');
});

test('statistics rank from ada tables and report online time unsupported', function () {
    $stats = app(PlayerStatsRepository::class);
    $active = User::factory()->create();
    $idle = User::factory()->create();
    $staff = User::factory()->create();
    $respected = User::factory()->create();

    DB::table('player_data')->where('player_id', $active->id)->update(['achievement_score' => 5_000]);
    DB::table('player_data')->where('player_id', $idle->id)->update(['achievement_score' => 100]);
    DB::table('player_data')->where('player_id', $staff->id)->update(['achievement_score' => 9_000]);
    DB::table('player_respects')->insert([
        ['origin_player_id' => $active->id, 'target_player_id' => $respected->id],
        ['origin_player_id' => $idle->id, 'target_player_id' => $respected->id],
    ]);

    $ranked = $stats->topBy(Stat::AchievementScore, 10, [$staff->id, $respected->id])
        ->map(fn (LeaderboardEntry $entry) => $entry->user->id)
        ->all();

    expect($ranked)->toBe([$active->id, $idle->id])
        ->and($stats->supports(Stat::AchievementScore))->toBeTrue()
        ->and($stats->supports(Stat::OnlineTime))->toBeFalse()
        ->and($stats->topBy(Stat::RespectsReceived, 5)->first()->user->id)->toBe($respected->id)
        ->and($stats->topBy(Stat::OnlineTime, 5))->toBeEmpty();
});

test('a ranked leaderboard loads its users in a fixed number of queries', function () {
    User::factory()->count(6)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    app(PlayerStatsRepository::class)->topBy(Stat::AchievementScore, 6);
    $queries = count(DB::getQueryLog());

    DB::disableQueryLog();

    // One ranking query, one user lookup, and the hydration pair. Loading
    // users one by one is the regression this guards against.
    expect($queries)->toBeLessThanOrEqual(4);
});

test('rooms report the door policy ada stores beside them', function (int $accessType, string $state) {
    $user = User::factory()->create();
    $roomId = makeAdaRoom($user->id, $accessType);

    // The home page badges a room by this value, so reporting every room as
    // open would mark locked and password rooms as freely enterable.
    expect(app(RoomRepository::class)->forHome($user)->first())
        ->toEqual(new RoomSummary($roomId, 'Ada room', 'EF room', $state));
})->with([
    'open' => [0, 'open'],
    'doorbell' => [1, 'locked'],
    'password' => [2, 'password'],
    'invisible' => [3, 'invisible'],
]);

test('ada never touches the arcturus player settings table', function () {
    $settings = app(PlayerSettingsRepository::class);
    $user = User::factory()->create();

    $settings->setCanChangeName($user, true);

    expect($settings->canChangeName($user))->toBeFalse()
        ->and(Schema::hasTable('users_settings'))->toBeFalse();
});

test('the arcturus installer refuses a database ada owns', function () {
    // Importing the Arcturus base dump over a live Ada database would destroy
    // it, so the installer has to recognise the foreign schema and stop.
    $installers = app(EmulatorManager::class);

    expect($installers->driver('arcturus')->installer()->prepare(silentCommand()))->toBeFalse()
        ->and($installers->driver('ada')->installer()->prepare(silentCommand()))->toBeTrue();
});

test('ada base roles can reach every seeded permission', function () {
    // Ada's base roles stop at Admin, while the seeded ladder was written for
    // Arcturus and runs past it. An owner who cannot reach their own owner
    // permissions has no error to go on - the feature is simply absent.
    $ceiling = app(RankRepository::class)->highestRank();

    expect($ceiling)->toBeGreaterThan(0);

    (new WebsitePermissionSeeder)->run();
    (new HousekeepingPermissionSeeder)->run();

    expect(WebsitePermission::max('min_rank'))->toBeLessThanOrEqual($ceiling)
        ->and(WebsiteHousekeepingPermission::max('min_rank'))->toBeLessThanOrEqual($ceiling);

    $owner = User::factory()->create(['rank' => $ceiling]);
    $player = User::factory()->create(['rank' => 1]);

    expect(app(HousekeepingPermissionsService::class)->allows($owner, 'can_access_housekeeping'))->toBeTrue()
        ->and(app(PermissionsService::class)->allows($owner, 'housekeeping_access'))->toBeTrue()
        ->and(app(HousekeepingPermissionsService::class)->allows($player, 'can_access_housekeeping'))->toBeFalse()
        ->and(app(PermissionsService::class)->allows($player, 'housekeeping_access'))->toBeFalse();
});

test('the ada installer waits on a database ada has not finished building', function () {
    // Ada creates its schema through EF migrations on first boot, so pointing
    // the installer at a database it has not touched yet is a matter of
    // ordering, not a broken setup. Non-interactively there is nobody to wait
    // for, so it reports and stops instead of blocking a scripted install.
    expect(app(EmulatorManager::class)->driver('ada')->installer()->prepare(silentCommand()))->toBeTrue();

    Schema::rename('player_navigator_settings', 'player_navigator_settings_hidden');

    try {
        expect(app(EmulatorManager::class)->driver('ada')->installer()->prepare(silentCommand()))->toBeFalse();
    } finally {
        Schema::rename('player_navigator_settings_hidden', 'player_navigator_settings');
    }
});

test('a grant held up by a competing one still leaves a single row', function () {
    // The race is read-then-insert with no constraint behind it, so a
    // sequential duplicate cannot reproduce it. Holding the lock and writing
    // the row underneath stands in for the competing process: the grant has
    // to notice the row appeared while it was waiting.
    $badges = app(BadgeRepository::class);
    $user = User::factory()->create();
    $code = 'ACH_Race';

    $lock = Cache::lock("ada-badge-grant:{$user->id}:" . sha1($code), 10);

    expect($lock->get())->toBeTrue();

    $badgeId = DB::table('badges')->insertGetId(['code' => $code]);
    DB::table('player_badges')->insert(['player_id' => $user->id, 'badge_id' => $badgeId, 'slot' => 0]);

    $lock->release();

    $badges->grant($user, $code);

    expect($badges->codes($user))->toBe([$code])
        ->and(DB::table('player_badges')->where('player_id', $user->id)->count())->toBe(1);
});

test('a grant that cannot take the lock does not write a duplicate', function () {
    $badges = app(BadgeRepository::class);
    $user = User::factory()->create();
    $code = 'ACH_Contended';

    $badges->grant($user, $code);

    // A competitor holding the lock past the wait window must make the grant
    // fail loudly rather than fall through and insert a second row.
    $lock = Cache::lock("ada-badge-grant:{$user->id}:" . sha1($code), 30);
    expect($lock->get())->toBeTrue();

    try {
        expect(fn () => $badges->grant($user, $code))->toThrow(LockTimeoutException::class);
    } finally {
        $lock->release();
    }

    expect(DB::table('player_badges')->where('player_id', $user->id)->count())->toBe(1);
});

test('ada grants a large quantity in bounded chunks', function () {
    $furniture = app(FurnitureRepository::class);
    $itemId = makeAdaFurnitureItem('Ada bulk chair');
    $user = User::factory()->create();
    $amount = 1200;

    $inserts = 0;
    DB::listen(function ($query) use (&$inserts): void {
        if (str_starts_with(strtolower(ltrim($query->sql)), 'insert into `player_furniture_items`')) {
            $inserts++;
        }
    });

    $furniture->grant($user, $itemId, $amount);

    expect(DB::table('player_furniture_items')->where('player_id', $user->id)->count())->toBe($amount)
        // Matches the Arcturus driver: ceil(1200 / 500) statements, never one
        // per item, so a big package cannot exceed max_allowed_packet.
        ->and($inserts)->toBe(3);
});

test('ada ignores a grant of nothing', function () {
    $furniture = app(FurnitureRepository::class);
    $itemId = makeAdaFurnitureItem('Ada empty grant');
    $user = User::factory()->create();

    $furniture->grant($user, $itemId, 0);
    $furniture->grant($user, $itemId, -5);

    expect(DB::table('player_furniture_items')->where('player_id', $user->id)->count())->toBe(0);
});
