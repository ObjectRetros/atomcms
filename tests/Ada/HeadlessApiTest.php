<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    config(['atom.mode' => 'headless']);
    $this->member = User::factory()->create(['rank' => 1, 'website_balance' => 1000]);
});

test('ada headless catalogs use driver capabilities and private account projection', function () {
    $this->getJson('/api/v1/bootstrap')->assertOk()->assertJsonPath('data.emulator', 'ada')->assertJsonPath('data.features', ['rare-values'])->assertJsonCount(0, 'data.latest_photos');
    $this->actingAs($this->member);
    foreach (['me', 'staff', 'teams', 'leaderboards', 'shop', 'applications', 'support', 'homes/' . $this->member->username] as $path) {
        $this->getJson('/api/v1/' . $path)->assertOk()->assertJsonStructure(['data']);
    }
    $this->getJson('/api/v1/photos')->assertNotFound()->assertJsonPath('code', 'feature_unavailable');
    $this->getJson('/api/v1/rare-values')->assertOk();
});

test('ada purchases return replayable receipts with one driver credit grant', function () {
    $package = makePackage();
    $initialCredits = $this->member->credits;
    $this->actingAs($this->member);
    $url = '/api/v1/shop/packages/' . $package->id . '/purchases';
    $first = $this->postJson($url, [], ['Idempotency-Key' => 'ada-purchase'])->assertCreated()->json();
    $this->postJson($url, [], ['Idempotency-Key' => 'ada-purchase'])->assertCreated()->assertExactJson($first);
    expect($this->member->fresh()->website_balance)->toBe(500)->and($this->member->fresh()->credits)->toBe($initialCredits + 200);
});

test('ada home badge widget exposes normalized owned badge data without rendering', function () {
    app(BadgeRepository::class)->grant($this->member, 'ADM');
    $definition = HomeItem::create(['name' => 'My Badges', 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'badges.png']);
    $item = $this->member->homeItems()->create(['home_item_id' => $definition->id, 'placed' => true]);
    $this->getJson('/api/v1/homes/' . $this->member->username . '/widgets/' . $item->id)->assertOk()->assertJsonPath('data.type', 'my-badges')->assertJsonPath('data.content.items.0.code', 'ADM');
});

test('ada public friend home and leaderboard projections read live emulator presence and motto', function () {
    $friend = User::factory()->create(['rank' => 1, 'online' => false, 'motto' => 'Stale motto']);
    DB::table('player_data')->where('player_id', $friend->id)->update(['is_online' => true]);
    DB::table('player_avatar_data')->where('player_id', $friend->id)->update(['motto' => 'Live motto']);
    DB::table('player_friendships')->insert(['origin_player_id' => $this->member->id, 'target_player_id' => $friend->id, 'status' => 2, 'created_at' => now()]);
    $this->member->receivedHomeMessages()->create(['user_id' => $friend->id, 'content' => 'Hello']);
    app(CurrencyRepository::class)->give($friend, CurrencyTypes::Credits, 1000000);
    $expected = ['id' => $friend->id, 'username' => $friend->username, 'motto' => 'Live motto', 'look' => $friend->look, 'online' => true];

    $this->actingAs($this->member)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.online_friends.0', $expected);
    foreach (['My Friends' => 'data.content.items.0', 'My Guestbook' => 'data.content.0.author'] as $name => $path) {
        $definition = HomeItem::create(['name' => $name, 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'widget.png']);
        $item = $this->member->homeItems()->create(['home_item_id' => $definition->id, 'placed' => true]);
        $this->getJson('/api/v1/homes/' . $this->member->username . '/widgets/' . $item->id)->assertOk()->assertJsonPath($path, $expected);
    }
    $this->getJson('/api/v1/leaderboards')->assertOk()->assertJsonPath('data.credits.0.user', $expected);
});
