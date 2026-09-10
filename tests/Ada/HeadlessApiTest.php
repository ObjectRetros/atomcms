<?php

use App\Emulator\Contracts\BadgeRepository;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Articles\WebsiteArticle;
use App\Models\Community\RareValue\WebsiteRareValue;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
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

test('ada ban details preserve permanent expiry and account isolation', function () {
    DB::table('player_bans')->insert(['creator_id' => $this->member->id, 'player_id' => $this->member->id, 'reason' => 'Permanent restriction', 'created_at' => now(), 'expires_at' => null]);
    $this->actingAs($this->member)->getJson('/api/v1/ban')->assertOk()->assertExactJson(['data' => ['type' => 'account', 'ban_reason' => 'Permanent restriction', 'ban_expire' => null]]);
    $this->actingAs(User::factory()->create())->getJson('/api/v1/ban')->assertOk()->assertExactJson(['data' => null]);
    auth()->logout();
    $this->getJson('/api/v1/ban')->assertOk()->assertExactJson(['data' => null]);
    DB::table('banned_ip_addresses')->insert(['creator_id' => $this->member->id, 'ip_address' => '127.0.0.1', 'reason' => 'Permanent address restriction', 'created_at' => now(), 'expires_at' => null]);
    $this->getJson('/api/v1/ban')->assertOk()->assertExactJson(['data' => ['type' => 'ip', 'ban_reason' => 'Permanent address restriction', 'ban_expire' => null]]);
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

test('ada account referrals expose configured reward and progress for the theme', function (?int $total) {
    setSetting('referrals_needed', '3');
    setSetting('referral_reward_amount', '10');
    if ($total !== null) {
        $this->member->referrals()->create(['referrals_total' => $total]);
    }

    $this->actingAs($this->member)->getJson('/api/v1/me')->assertOk()
        ->assertJsonPath('data.referrals_total', $total ?? 0)
        ->assertJsonPath('data.referral_threshold', 3)
        ->assertJsonPath('data.referral_reward_amount', 10)
        ->assertJsonPath('data.referrals_needed', 3 - ($total ?? 0));
})->with([null, 4]);

test('ada home badge widget exposes normalized owned badge data without rendering', function () {
    app(BadgeRepository::class)->grant($this->member, 'ADM');
    $definition = HomeItem::create(['name' => 'My Badges', 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'badges.png']);
    $item = $this->member->homeItems()->create(['home_item_id' => $definition->id, 'placed' => true]);
    $this->getJson('/api/v1/homes/' . $this->member->username . '/widgets/' . $item->id)->assertOk()->assertJsonPath('data.type', 'my-badges')->assertJsonPath('data.content.items.0.code', 'ADM');
});

test('ada public home reads the member registration date from the emulator', function () {
    DB::table('players')->where('id', $this->member->id)->update(['created_at' => '2024-01-02 03:04:05']);

    $this->getJson('/api/v1/homes/' . $this->member->username)->assertOk()
        ->assertJsonPath('data.member_since', '2024-01-02T03:04:05+00:00')
        ->assertJsonMissingPath('data.user.mail');
});

test('ada rare values expose artwork without arcturus limited edition columns', function () {
    setSetting('furniture_icons_path', 'https://images.example.com/furniture');
    $category = WebsiteRareValueCategory::create(['name' => 'Ada rares', 'badge' => 'LTD', 'priority' => 1]);
    $value = WebsiteRareValue::create(['category_id' => $category->id, 'item_id' => 230, 'name' => 'Ada chair', 'furniture_icon' => 'chair.png']);

    $this->actingAs($this->member)->getJson('/api/v1/rare-values?category=' . $category->id)->assertOk()
        ->assertJsonPath('data.0.badge', 'LTD')
        ->assertJsonPath('data.0.values.0.id', $value->id)
        ->assertJsonPath('data.0.values.0.icon', 'https://images.example.com/furniture/chair.png')
        ->assertJsonPath('data.0.values.0.item_id', 230)
        ->assertJsonPath('data.0.values.0.is_limited', false);
});

test('ada public friend home and leaderboard projections read live emulator presence and motto', function () {
    $friend = User::factory()->create(['rank' => 1, 'online' => false, 'motto' => 'Stale motto', 'last_online' => 1]);
    DB::table('player_data')->where('player_id', $friend->id)->update(['is_online' => true, 'last_online' => '2026-09-01 12:00:00']);
    DB::table('player_avatar_data')->where('player_id', $friend->id)->update(['motto' => 'Live motto']);
    DB::table('player_friendships')->insert(['origin_player_id' => $this->member->id, 'target_player_id' => $friend->id, 'status' => 2, 'created_at' => now()]);
    $this->member->receivedHomeMessages()->create(['user_id' => $friend->id, 'content' => 'Hello']);
    app(CurrencyRepository::class)->give($friend, CurrencyTypes::Credits, 1000000);
    $expected = ['id' => $friend->id, 'username' => $friend->username, 'motto' => 'Live motto', 'look' => $friend->look, 'online' => true];

    $this->actingAs($this->member)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.online_friends.0', [...$expected, 'last_online' => 1788264000]);
    foreach (['My Friends' => 'data.content.items.0', 'My Guestbook' => 'data.content.0.author'] as $name => $path) {
        $definition = HomeItem::create(['name' => $name, 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'widget.png']);
        $item = $this->member->homeItems()->create(['home_item_id' => $definition->id, 'placed' => true]);
        $this->getJson('/api/v1/homes/' . $this->member->username . '/widgets/' . $item->id)->assertOk()->assertJsonPath($path, $expected);
    }
    $this->getJson('/api/v1/leaderboards')->assertOk()->assertJsonPath('data.credits.0.user', $expected);
});

test('ada article presentation uses role names and the driver background default', function () {
    DB::table('roles')->where('id', 1)->update(['name' => 'Ada member']);
    $article = WebsiteArticle::create(['user_id' => $this->member->id, 'title' => 'Ada author', 'short_story' => 'Preview', 'full_story' => 'Content']);

    $this->getJson('/api/v1/articles/' . $article->slug)->assertOk()
        ->assertJsonPath('author_display', ['rank_name' => 'Ada member', 'background_url' => asset('assets/images/staff-bg.png')]);
});
