<?php

use App\Contracts\Rcon;
use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\BanRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\FurnitureRepository;
use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Contracts\RankRepository;
use App\Emulator\Contracts\RoomRepository;
use App\Emulator\Drivers\Ada\AdaBadgeRepository;
use App\Emulator\Drivers\Ada\AdaBanRepository;
use App\Emulator\Drivers\Ada\AdaCurrencyRepository;
use App\Emulator\Drivers\Ada\AdaDriver;
use App\Emulator\Drivers\Ada\AdaFurnitureRepository;
use App\Emulator\Drivers\Ada\AdaPlayerRepository;
use App\Emulator\Drivers\Ada\AdaPlayerSettingsRepository;
use App\Emulator\Drivers\Ada\AdaPlayerStatsRepository;
use App\Emulator\Drivers\Ada\AdaRankRepository;
use App\Emulator\Drivers\Ada\AdaRoomRepository;
use App\Emulator\Drivers\Arcturus\ArcturusDriver;
use App\Emulator\EmulatorManager;
use App\Enums\CurrencyTypes;
use App\Exceptions\RconConnectionException;
use App\Filament\Resources\Atom\Permissions\PermissionResource;
use App\Filament\Resources\Hotel\AdaCatalogItems\AdaCatalogItemResource;
use App\Filament\Resources\Hotel\AdaCatalogPages\AdaCatalogPageResource;
use App\Filament\Resources\Hotel\AdaPlayerMessages\AdaPlayerMessageResource;
use App\Filament\Resources\Hotel\AdaRoles\AdaRoleResource;
use App\Filament\Resources\Hotel\AdaRoomChatMessages\AdaRoomChatMessageResource;
use App\Filament\Resources\Hotel\AdaServerLocaleTexts\AdaServerLocaleTextResource;
use App\Filament\Resources\Hotel\AdaServerSettings\AdaServerSettingsResource;
use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use App\Filament\Resources\Hotel\ChatlogPrivates\ChatlogPrivateResource;
use App\Filament\Resources\Hotel\ChatlogRooms\ChatlogRoomResource;
use App\Filament\Resources\Hotel\EmulatorSettings\EmulatorSettingResource;
use App\Filament\Resources\Hotel\EmulatorTexts\EmulatorTextResource;
use App\Filament\Resources\User\AdaIpBans\AdaIpBanResource;
use App\Filament\Resources\User\AdaPlayerBans\AdaPlayerBanResource;
use App\Filament\Resources\User\Bans\BanResource;
use App\Models\Ada\AdaPlayerMessage;
use App\Models\Ada\AdaRoomChatMessage;
use App\Models\Ada\AdaServerLocaleText;
use App\Models\Ada\AdaServerSettings;
use App\Models\Articles\WebsiteArticle;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\User;
use App\Services\Community\StaffService;
use App\Services\User\UserApiService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    installHotel();
    setSetting('start_duckets', '0');
    setSetting('start_diamonds', '0');
    setSetting('start_points', '0');
});

test('ada binds every emulator contract and plus is removed', function () {
    $drivers = config('emulator.drivers');

    // Arcturus remains the shipped default; this suite selects Ada.
    expect(config('emulator.drivers.arcturus'))->toBe(ArcturusDriver::class)
        ->and(config('emulator.driver'))->toBe('ada')
        ->and($drivers)->toHaveKeys(['arcturus', 'ada'])
        ->and($drivers)->not->toHaveKey('plus')
        ->and($drivers['arcturus'])->toBe(ArcturusDriver::class)
        ->and($drivers['ada'])->toBe(AdaDriver::class)
        ->and(app(EmulatorManager::class)->driver('ada')->bindings())->toMatchArray([
            BadgeRepository::class => AdaBadgeRepository::class,
            BanRepository::class => AdaBanRepository::class,
            CurrencyRepository::class => AdaCurrencyRepository::class,
            FurnitureRepository::class => AdaFurnitureRepository::class,
            PlayerRepository::class => AdaPlayerRepository::class,
            PlayerSettingsRepository::class => AdaPlayerSettingsRepository::class,
            PlayerStatsRepository::class => AdaPlayerStatsRepository::class,
            RankRepository::class => AdaRankRepository::class,
            RoomRepository::class => AdaRoomRepository::class,
        ]);

    expect(app(BadgeRepository::class))->toBeInstanceOf(AdaBadgeRepository::class)
        ->and(app(BanRepository::class))->toBeInstanceOf(AdaBanRepository::class)
        ->and(app(CurrencyRepository::class))->toBeInstanceOf(AdaCurrencyRepository::class)
        ->and(app(FurnitureRepository::class))->toBeInstanceOf(AdaFurnitureRepository::class)
        ->and(app(PlayerRepository::class))->toBeInstanceOf(AdaPlayerRepository::class)
        ->and(app(PlayerSettingsRepository::class))->toBeInstanceOf(AdaPlayerSettingsRepository::class)
        ->and(app(PlayerStatsRepository::class))->toBeInstanceOf(AdaPlayerStatsRepository::class)
        ->and(app(RankRepository::class))->toBeInstanceOf(AdaRankRepository::class)
        ->and(app(RoomRepository::class))->toBeInstanceOf(AdaRoomRepository::class);
});

test('ada creates and synchronizes the complete player aggregate', function () {
    $user = User::factory()->create([
        'motto' => 'Ada player',
        'look' => 'hd-180-1.hr-100-61',
        'gender' => 'F',
        'credits' => 750,
        'rank' => 1,
    ]);

    expect(DB::table('players')->where('id', $user->id)->value('email'))->toBe($user->mail)
        ->and(DB::table('player_avatar_data')->where('player_id', $user->id)->value('figure_code'))->toBe($user->look)
        ->and(DB::table('player_data')->where('player_id', $user->id)->value('credit_balance'))->toBe(750)
        ->and(DB::table('player_game_settings')->where('player_id', $user->id)->exists())->toBeTrue()
        ->and(DB::table('player_navigator_settings')->where('player_id', $user->id)->exists())->toBeTrue()
        ->and(DB::table('player_website_data')->where('player_id', $user->id)->exists())->toBeTrue()
        ->and(DB::table('player_role')->where('player_id', $user->id)->value('role_id'))->toBe(1);

    // rank is guarded against mass assignment, so the trusted paths that set
    // it (the housekeeping editor, seeders) force-fill - do the same here.
    $user->forceFill([
        'mail' => 'ada@example.com',
        'motto' => 'Updated motto',
        'password' => Hash::make('new-password'),
        'rank' => 5,
    ])->save();

    expect(DB::table('players')->where('id', $user->id)->value('email'))->toBe('ada@example.com')
        ->and(DB::table('player_avatar_data')->where('player_id', $user->id)->value('motto'))->toBe('Updated motto')
        ->and(DB::table('player_role')->where('player_id', $user->id)->value('role_id'))->toBe(5);
});

test('ada maps cms ranks to the nearest available emulator role', function () {
    $user = User::factory()->create(['rank' => 9]);

    expect(DB::table('player_role')->where('player_id', $user->id)->value('role_id'))->toBe(6);
});

test('ada registration creates a login-ready EF player', function () {
    $this->post('/register', [
        'username' => 'AdaGuest',
        'mail' => 'ada-guest@example.com',
        'password' => 'Sup3rSecret!',
        'password_confirmation' => 'Sup3rSecret!',
        'terms' => true,
    ])->assertRedirect();

    $user = User::where('username', 'AdaGuest')->firstOrFail();

    expect(auth()->id())->toBe($user->id)
        ->and(DB::table('players')->where('id', $user->id)->value('username'))->toBe('AdaGuest')
        ->and(DB::table('player_data')->where('player_id', $user->id)->exists())->toBeTrue()
        ->and(DB::table('player_sso_tokens')->where('player_id', $user->id)->exists())->toBeFalse();
});

test('ada removes the EF player aggregate when an Atom user is deleted', function () {
    $user = User::factory()->create();

    // Ada cascades most player tables but restricts wardrobe items and tags,
    // so an account that saved either would otherwise fail on a foreign key.
    DB::table('player_wardrobe_items')->insert([
        'player_id' => $user->id, 'slot_id' => 1, 'figure_code' => 'hd-180-1', 'gender' => 0,
    ]);
    DB::table('player_tags')->insert(['player_id' => $user->id, 'name' => 'builder']);

    $user->delete();

    expect(DB::table('players')->where('id', $user->id)->exists())->toBeFalse()
        ->and(DB::table('player_wardrobe_items')->where('player_id', $user->id)->exists())->toBeFalse()
        ->and(DB::table('player_tags')->where('player_id', $user->id)->exists())->toBeFalse();
});

test('ada resolves online friendships in both directions', function () {
    $user = User::factory()->create();
    $outgoing = User::factory()->create();
    $incoming = User::factory()->create();
    $offline = User::factory()->create();
    $pending = User::factory()->create();

    DB::table('player_data')->whereIn('player_id', [$outgoing->id, $incoming->id, $pending->id])->update([
        'is_online' => true,
    ]);
    DB::table('player_friendships')->insert([
        ['origin_player_id' => $user->id, 'target_player_id' => $outgoing->id, 'status' => 2, 'created_at' => now()],
        ['origin_player_id' => $incoming->id, 'target_player_id' => $user->id, 'status' => 2, 'created_at' => now()],
        ['origin_player_id' => $user->id, 'target_player_id' => $offline->id, 'status' => 2, 'created_at' => now()],
        ['origin_player_id' => $user->id, 'target_player_id' => $pending->id, 'status' => 1, 'created_at' => now()],
    ]);

    $expected = collect([$outgoing->id, $incoming->id])->sort()->values()->all();

    expect($user->getOnlineFriends()->pluck('id')->sort()->values()->all())->toBe($expected);

    $user->loadFriendsForHome('home.show');

    // Accepted friendships list regardless of presence; only the online widget
    // filters on it.
    expect($user->friends->getCollection()->pluck('user.id')->sort()->values()->all())
        ->toBe(collect([$outgoing->id, $incoming->id, $offline->id])->sort()->values()->all());
});

test('ada reads presence from its own tables, not the mirrored users column', function () {
    $online = User::factory()->create();
    $offline = User::factory()->create();

    // The users column is a compatibility mirror Ada never writes to. Set it
    // to the opposite of the truth to prove nothing reads it.
    DB::table('users')->where('id', $online->id)->update(['online' => '0']);
    DB::table('users')->where('id', $offline->id)->update(['online' => '1']);
    DB::table('player_data')->where('player_id', $online->id)->update(['is_online' => true]);

    $api = app(UserApiService::class);

    expect($api->onlineUserCount())->toBe(1)
        ->and($api->onlineUsers(['id', 'username'])->pluck('id')->all())->toBe([$online->id])
        ->and(User::findOrFail($online->id)->online)->toBeTrue()
        ->and(User::findOrFail($offline->id)->online)->toBeFalse();
});

test('ada refreshes users even when a query selects a column subset', function () {
    $user = User::factory()->create(['motto' => 'mirror motto', 'look' => 'mirror-look']);

    DB::table('player_data')->where('player_id', $user->id)->update(['is_online' => true]);
    DB::table('player_avatar_data')->where('player_id', $user->id)->update([
        'motto' => 'live motto',
        'figure_code' => 'live-look',
    ]);

    // The public API selects username/motto/look and no key. Without the key
    // there is nothing to match against Ada, and the stale mirror would be
    // served instead.
    $api = app(UserApiService::class);

    expect($api->fetchUser($user->username)?->motto)->toBe('live motto')
        ->and($api->onlineUsers()->first()?->look)->toBe('live-look');
});

test('ada refreshes a whole result set in a fixed number of queries', function () {
    User::factory()->count(15)->create();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $users = User::query()->limit(15)->get();
    $queries = count(DB::getQueryLog());

    DB::disableQueryLog();

    // One query for the users, one for the Ada aggregates, one for the roles.
    // Anything that scales with the result count is an N+1 regression.
    expect($users)->toHaveCount(15)
        ->and($queries)->toBe(3);
});

test('ada only writes the aggregates whose columns actually changed', function () {
    $user = User::factory()->create();

    // Ada owns the live values; a CMS-only save must not stamp them back.
    DB::table('player_avatar_data')->where('player_id', $user->id)->update(['motto' => 'Set in game']);
    DB::table('player_website_data')->where('player_id', $user->id)->update(['last_ip' => '203.0.113.9']);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $user->forceFill(['hidden_staff' => true])->save();

    $writes = collect(DB::getQueryLog())
        ->filter(fn (array $query) => str_starts_with(strtolower(trim($query['query'])), 'update'));

    DB::disableQueryLog();

    expect($writes)->toHaveCount(1)
        ->and($writes->first()['query'])->toContain('`users`')
        ->and(DB::table('player_avatar_data')->where('player_id', $user->id)->value('motto'))->toBe('Set in game')
        ->and(DB::table('player_website_data')->where('player_id', $user->id)->value('last_ip'))->toBe('203.0.113.9');
});

test('ada grants club membership on registration when the hotel offers it', function () {
    setSetting('give_hc_on_register', '1');
    setSetting('hc_on_register_duration', (string) (60 * 60 * 24 * 31));
    $user = User::factory()->create();

    $subscription = DB::table('player_subscriptions')
        ->join('subscriptions', 'subscriptions.id', '=', 'player_subscriptions.subscription_id')
        ->where('player_subscriptions.player_id', $user->id)
        ->first(['subscriptions.name', 'player_subscriptions.expires_at']);

    expect($subscription?->name)->toBe('HABBO_CLUB')
        ->and(Carbon::parse($subscription->expires_at)->isAfter(now()->addDays(30)))->toBeTrue();
});

test('ada does not grant club membership when the hotel does not offer it', function () {
    setSetting('give_hc_on_register', '0');
    $user = User::factory()->create();

    expect(DB::table('player_subscriptions')->where('player_id', $user->id)->exists())->toBeFalse();
});

test('ada reports rcon as unavailable and refuses to pretend otherwise', function () {
    $user = User::factory()->create();
    $rcon = app(Rcon::class);

    // Call sites branch on isConnected(); anything that skips that check must
    // fail loudly rather than drop the grant on the floor.
    expect($rcon->isConnected())->toBeFalse()
        ->and(fn () => $rcon->giveCurrency($user, CurrencyTypes::Credits, 100))
        ->toThrow(RconConnectionException::class)
        ->and(fn () => $rcon->giveBadge($user, 'ACH_Test'))
        ->toThrow(RconConnectionException::class)
        ->and(fn () => $rcon->sendCommand('anything'))
        ->toThrow(RconConnectionException::class);
});

test('ada roles drive the public staff listing', function () {
    setSetting('enable_caching', '0');
    setSetting('min_staff_rank', '5');

    $staff = User::factory()->create(['rank' => 5, 'hidden_staff' => false]);
    $viewer = User::factory()->create(['rank' => 1]);
    $positions = app(StaffService::class)->fetchStaffPositions($viewer);

    expect($positions->firstWhere('id', 5)?->rank_name)->toBe('Moderator')
        ->and($positions->flatMap->users->pluck('id'))->toContain($staff->id);
});

test('ada role display fields render on an article page', function () {
    setSetting('theme', 'atom');

    $author = User::factory()->create(['rank' => 6]);
    $article = WebsiteArticle::create([
        'user_id' => $author->id,
        'title' => 'Ada announcement',
        'short_story' => 'Ada is ready.',
        'full_story' => '<p>Ada is ready.</p>',
        'image' => '',
    ]);

    $this->get(route('article.show', $article->slug))
        ->assertOk()
        ->assertSee('Admin');
});

test('ada roles back staff positions and applications', function () {

    $user = User::factory()->create(['rank' => 6]);
    $position = WebsiteOpenPosition::create([
        'position_kind' => 'rank',
        'permission_id' => 6,
        'description' => 'Hotel administrator',
    ]);
    $application = WebsiteStaffApplications::create([
        'user_id' => $user->id,
        'rank_id' => 6,
        'content' => 'I would like to help.',
        'status' => 'pending',
    ]);

    expect($position->permission?->rank_name)->toBe('Admin')
        ->and($application->rank?->rank_name)->toBe('Admin');
});

test('ada renders the shared cms page surface', function () {
    setSetting('theme', 'atom');
    $user = User::factory()->create();

    foreach ([
        'article.index',
        'help-center.rules.index',
    ] as $route) {
        $this->get(route($route))->assertOk();
    }

    $this->actingAs($user);

    foreach ([
        'staff.index',
        'teams.index',
        'staff-applications.index',
        'team-applications.index',
        'help-center.index',
        'help-center.ticket.create',
        'leaderboard.index',
        'shop.index',
        'me.show',
        'settings.account.show',
    ] as $route) {
        $this->get(route($route))->assertOk();
    }

    $this->get(route('home.show', $user->username))->assertOk();
});

test('ada hides surface it has no schema for', function () {
    setSetting('theme', 'atom');
    $user = User::factory()->create();

    // Ada persists no camera photos, so the gallery redirects instead of
    // rendering a gallery that can never fill up.
    $this->actingAs($user)
        ->get(route('photos.index'))
        ->assertRedirect(route('welcome'));

    $this->actingAs($user)
        ->get(route('leaderboard.index'))
        ->assertOk()
        ->assertDontSee(__('Hours online'));
});

test('ada rare values render without arcturus catalog columns', function () {
    setSetting('theme', 'atom');
    $user = User::factory()->create();
    $category = WebsiteRareValueCategory::create([
        'name' => 'Ada rares',
        'badge' => '',
        'priority' => 1,
    ]);
    $value = WebsiteRareValue::create([
        'category_id' => $category->id,
        'item_id' => 230,
        'name' => 'Ada chair',
        'credit_value' => '10',
        'currency_value' => '0',
        'currency_type' => 0,
        'furniture_icon' => 'chair.png',
    ]);

    $this->actingAs($user)
        ->get(route('values.value', $value))
        ->assertOk();

    expect($value->isLimitedEdition())->toBeFalse();
});

test('ada hydrates live EF player state into Atom users', function () {
    $user = User::factory()->create();

    DB::table('player_avatar_data')->where('player_id', $user->id)->update([
        'figure_code' => 'hd-999-1',
        'motto' => 'From Ada',
    ]);
    DB::table('player_data')->where('player_id', $user->id)->update([
        'credit_balance' => 4321,
        'is_online' => true,
        'last_online' => now(),
    ]);
    DB::table('player_website_data')->where('player_id', $user->id)->update([
        'last_login' => now()->subMinute(),
    ]);

    $hydrated = User::findOrFail($user->id);

    expect($hydrated->look)->toBe('hd-999-1')
        ->and($hydrated->motto)->toBe('From Ada')
        ->and($hydrated->credits)->toBe(4321)
        ->and($hydrated->online)->toBeTrue()
        ->and($hydrated->last_online)->toBeGreaterThan(0)
        ->and($hydrated->last_login)->toBeGreaterThan(0);
});

test('ada validates player fields against EF column lengths', function () {
    $user = User::factory()->create(['online' => '0']);

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => str_repeat('a', 45) . '@example.com',
            'motto' => str_repeat('m', 51),
            'current_password' => 'password',
        ])
        ->assertSessionHasErrors(['mail', 'motto']);
});

test('an offline ada user can change their motto without rcon', function () {
    // Ada has no RCON bridge, so a driver that told the emulator about every
    // motto change would fail every one of them. The database write is the
    // whole change here.
    $user = User::factory()->create(['online' => false, 'motto' => 'Before']);

    $this->actingAs($user)
        ->put(route('settings.account.update'), ['mail' => $user->mail, 'motto' => 'After'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('settings.account.show'));

    expect($user->fresh()->motto)->toBe('After')
        ->and(DB::table('player_avatar_data')->where('player_id', $user->id)->value('motto'))->toBe('After');
});

test('an ada email and motto change apply together or not at all', function () {
    $user = User::factory()->create(['online' => false, 'motto' => 'Before']);
    $original = $user->mail;

    $this->actingAs($user)
        ->put(route('settings.account.update'), [
            'mail' => 'changed@example.com',
            'motto' => 'After',
            'current_password' => 'password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('settings.account.show'));

    $user->refresh();

    expect($user->mail)->toBe('changed@example.com')
        ->and($user->motto)->toBe('After')
        ->and($original)->not->toBe('changed@example.com')
        ->and(DB::table('players')->where('id', $user->id)->value('email'))->toBe('changed@example.com');
});

test('ada issues EF-compatible expiring one-time SSO tokens', function () {
    $user = User::factory()->create();

    $token = $user->ssoTicket();
    $stored = DB::table('player_sso_tokens')->where('token', $token)->first();

    expect($token)->not->toBe('')
        ->and($stored->player_id)->toBe($user->id)
        ->and($stored->used_at)->toBeNull()
        ->and(Carbon::parse($stored->expires_at)->isAfter(now()))->toBeTrue();
});

test('ada chat, private message, locale and server setting models match EF tables', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    DB::table('player_messages')->insert([
        'origin_player_id' => $sender->id,
        'target_player_id' => $receiver->id,
        'message' => 'Private hello',
        'created_at' => now(),
    ]);
    $roomId = makeAdaRoom($sender->id);
    DB::table('room_chat_messages')->insert([
        'room_id' => $roomId,
        'player_id' => $sender->id,
        'message' => 'Room hello',
        'chat_bubble_id' => 0,
        'emotion_id' => 0,
        'type_id' => 0,
        'created_at' => now(),
    ]);
    DB::table('server_settings')->insert([
        'player_welcome_message' => 'Welcome',
        'fair_currency_rewards' => true,
        'created_at' => now(),
    ]);

    AdaServerLocaleText::create(['key' => 'hotel.welcome', 'text' => 'Hello']);

    expect(AdaPlayerMessage::first()->sender->is($sender))->toBeTrue()
        ->and(AdaPlayerMessage::first()->receiver->is($receiver))->toBeTrue()
        ->and(AdaRoomChatMessage::first()->player->is($sender))->toBeTrue()
        ->and(AdaServerLocaleText::first()->text)->toBe('Hello')
        ->and(AdaServerSettings::first()->fair_currency_rewards)->toBeTrue();
});

test('housekeeping registers only schema-compatible resources for ada', function () {
    expect(BanResource::shouldRegisterNavigation())->toBeFalse()
        ->and(PermissionResource::shouldRegisterNavigation())->toBeFalse()
        ->and(CatalogEditorResource::shouldRegisterNavigation())->toBeFalse()
        ->and(ChatlogRoomResource::shouldRegisterNavigation())->toBeFalse()
        ->and(ChatlogPrivateResource::shouldRegisterNavigation())->toBeFalse()
        ->and(EmulatorSettingResource::shouldRegisterNavigation())->toBeFalse()
        ->and(EmulatorTextResource::shouldRegisterNavigation())->toBeFalse()
        ->and(AdaPlayerBanResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaIpBanResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaCatalogPageResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaCatalogItemResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaRoleResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaRoomChatMessageResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaPlayerMessageResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaServerSettingsResource::shouldRegisterNavigation())->toBeTrue()
        ->and(AdaServerLocaleTextResource::shouldRegisterNavigation())->toBeTrue();
});
