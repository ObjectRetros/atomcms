<?php

use App\Contracts\PaypalGateway;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Articles\WebsiteArticle;
use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Home\HomeItem;
use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;
use App\Models\WebsiteApiIdempotencyKey;
use App\Services\Shop\IdempotentOperation;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
    config(['atom.mode' => 'headless']);
    $this->member = User::factory()->create(['rank' => 1, 'website_balance' => 2000]);
});

test('bootstrap exposes configured public data and safe session identity', function () {
    setSetting('force_staff_2fa', '1');
    setSetting('min_staff_rank', '4');
    setSetting('rcon_ip', 'private-secret');
    $this->getJson('/api/v1/bootstrap')->assertOk()->assertJsonPath('data.viewer', null)->assertJsonPath('data.installed', true)->assertDontSee('private-secret');
    $this->actingAs($this->member)->getJson('/api/v1/bootstrap')->assertOk()->assertJsonPath('data.viewer.username', $this->member->username)->assertJsonMissingPath('data.viewer.mail');
});

test('public projections do not leak account secrets or balances', function () {
    $this->getJson('/api/v1/users/' . $this->member->username)->assertOk()->assertExactJson(['data' => [
        'id' => $this->member->id, 'username' => $this->member->username, 'motto' => (string) $this->member->motto, 'look' => $this->member->look, 'online' => false,
    ]]);
    $this->get('/api/v1/me')->assertUnauthorized()->assertJsonPath('code', 'unauthenticated');
    $this->actingAs($this->member)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.mail', $this->member->mail)->assertJsonPath('data.website_balance.amount_minor', 2000)->assertJsonMissingPath('data.password')->assertJsonMissingPath('data.auth_ticket');
});

test('all public and authenticated catalog read operations produce JSON', function () {
    foreach (['articles', 'users?q=abc', 'users/online', 'rules', 'homes/' . $this->member->username] as $path) {
        $this->getJson('/api/v1/' . $path)->assertOk()->assertJsonStructure(['data']);
    }
    $this->actingAs($this->member);
    foreach (['staff', 'teams', 'leaderboards', 'photos', 'applications', 'shop', 'shop/purchases', 'support', 'support/tickets', 'home-shop', 'homes/' . $this->member->username . '/inventory', 'badges', 'rare-values', 'me/sessions'] as $path) {
        $this->getJson('/api/v1/' . $path)->assertOk()->assertJsonStructure(['data']);
    }
});

test('article comments and reactions share persistence and reject forbidden edits', function () {
    $article = WebsiteArticle::create(['user_id' => $this->member->id, 'title' => 'API article', 'short_story' => 'A story', 'full_story' => '<p>Story</p>', 'can_comment' => true]);
    $this->getJson('/api/v1/articles/' . $article->slug)->assertOk()->assertJsonPath('data.author.username', $this->member->username);
    $this->actingAs($this->member)->postJson('/api/v1/articles/' . $article->slug . '/comments', ['comment' => 'Shared comment'])->assertCreated();
    $comment = $article->comments()->firstOrFail();
    $this->getJson('/api/v1/articles/' . $article->slug . '/comments')->assertOk()->assertJsonPath('data.0.comment', 'Shared comment');
    $this->postJson('/api/v1/articles/' . $article->slug . '/reactions', ['reaction' => 'heart'])->assertOk()->assertJsonPath('data.added', true);
    $this->postJson('/api/v1/articles/' . $article->slug . '/reactions', ['reaction' => 'invalid'])->assertUnprocessable();
    $this->actingAs(User::factory()->create(['rank' => 1]))->deleteJson('/api/v1/comments/' . $comment->id)->assertForbidden();
    $this->actingAs($this->member)->deleteJson('/api/v1/comments/' . $comment->id)->assertNoContent();
    $article->update(['can_comment' => false]);
    $this->postJson('/api/v1/articles/' . $article->slug . '/comments', ['comment' => 'Locked comment'])->assertUnprocessable();
});

test('account changes retain current password checks', function () {
    $this->actingAs($this->member)->putJson('/api/v1/me/account', ['mail' => 'changed@example.com', 'motto' => 'Changed'])->assertUnprocessable()->assertJsonValidationErrors('current_password');
    $this->putJson('/api/v1/me/account', ['mail' => $this->member->mail, 'motto' => 'Updated by API'])->assertNoContent();
    expect($this->member->fresh()->motto)->toBe('Updated by API');
    $this->putJson('/api/v1/me/password', ['password' => 'secure-password', 'password_confirmation' => 'secure-password'])->assertUnprocessable()->assertJsonValidationErrors('current_password');
});

test('a package retry returns the same receipt and charges once', function () {
    $package = makePackage(['stock' => 3]);
    $this->actingAs($this->member);
    $url = '/api/v1/shop/packages/' . $package->id . '/purchases';
    $first = $this->postJson($url, [], ['Idempotency-Key' => 'purchase-1'])->assertCreated()->assertJsonPath('data.charged.amount_minor', 500)->json('data');
    $this->postJson($url, [], ['Idempotency-Key' => 'purchase-1'])->assertCreated()->assertExactJson(['data' => $first]);
    $this->postJson($url, ['receiver' => $this->member->username], ['Idempotency-Key' => 'purchase-1'])->assertConflict();
    expect($this->member->fresh()->website_balance)->toBe(1500)->and(DB::table('website_shop_purchases')->count())->toBe(1)->and($package->fresh()->stock)->toBe(2);
    $this->postJson($url)->assertUnprocessable();
});

test('voucher redemption is shared and can only credit once', function () {
    WebsiteShopVoucher::create(['code' => 'API-VOUCHER', 'amount' => 100]);
    $this->actingAs($this->member)->postJson('/api/v1/shop/vouchers', ['code' => 'API-VOUCHER'])->assertOk()->assertJsonPath('data.credited.amount_minor', 100);
    $this->postJson('/api/v1/shop/vouchers', ['code' => 'API-VOUCHER'])->assertUnprocessable();
    expect($this->member->fresh()->website_balance)->toBe(2100);
});

test('paypal retries reuse the created order and another user cannot read it', function () {
    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('createOrder')->once()->with(Mockery::type('array'), Mockery::on(fn ($key) => str_starts_with($key, 'atom-order-')))->andReturn(['id' => 'API-ORDER', 'links' => [['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=API-ORDER']]]);
    $this->app->instance(PaypalGateway::class, $gateway);
    $this->actingAs($this->member);
    $first = $this->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'paypal-1'])->assertCreated()->assertJsonPath('data.amount_minor', 1000)->json();
    $this->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'paypal-1'])->assertCreated()->assertExactJson($first);
    $this->getJson('/api/v1/shop/paypal/orders/API-ORDER')->assertOk()->assertJsonPath('data.status', 'CREATED');
    $this->actingAs(User::factory()->create())->getJson('/api/v1/shop/paypal/orders/API-ORDER')->assertNotFound();
});

test('support enforces ticket ownership and closed replies', function () {
    $category = WebsiteHelpCenterCategory::create(['name' => 'Support', 'content' => 'General support']);
    $this->actingAs($this->member);
    $id = $this->postJson('/api/v1/support/tickets', ['category_id' => $category->id, 'title' => 'A support question', 'content' => 'A sufficiently detailed question'])->assertCreated()->json('data.id');
    $this->postJson('/api/v1/support/tickets/' . $id . '/replies', ['content' => 'More information follows'])->assertCreated();
    $this->postJson('/api/v1/support/tickets/' . $id . '/toggle-status')->assertNoContent();
    $this->postJson('/api/v1/support/tickets/' . $id . '/replies', ['content' => 'This ticket is now closed'])->assertForbidden();
    $this->actingAs(User::factory()->create(['rank' => 1]))->getJson('/api/v1/support/tickets/' . $id)->assertForbidden();
    $this->getJson('/api/v1/support/tickets')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/support/tickets?all=true')->assertForbidden();
});

test('home widgets return structured public data and enforce placement and ownership', function () {
    $widget = HomeItem::create(['name' => 'My Profile', 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'profile.png']);
    $placed = $this->member->homeItems()->create(['home_item_id' => $widget->id, 'placed' => true]);
    $path = '/api/v1/homes/' . $this->member->username;
    $this->getJson($path . '/widgets/' . $placed->id)->assertOk()->assertJsonPath('data.content.username', $this->member->username)->assertJsonMissingPath('data.content.mail');
    $this->actingAs(User::factory()->create())->getJson($path . '/inventory')->assertForbidden();
    $this->putJson($path, ['backgroundId' => 0, 'items' => []])->assertForbidden();
    $this->actingAs($this->member)->putJson($path, ['backgroundId' => 0, 'items' => [['id' => $placed->id, 'x' => 15, 'y' => 20, 'z' => 1]]])->assertNoContent();
    expect($placed->fresh()->x)->toBe(15);
    $placed->update(['placed' => false]);
    $this->getJson($path . '/widgets/' . $placed->id)->assertOk();
    $this->actingAs(User::factory()->create())->getJson($path . '/widgets/' . $placed->id)->assertNotFound();
});

test('game launch issues a private driver ticket and guest launch is denied', function () {
    setSetting('findretros_enabled', '0');
    $this->postJson('/api/v1/client/launch')->assertUnauthorized();
    $ticket = $this->actingAs($this->member)->postJson('/api/v1/client/launch', ['client' => 'nitro'])->assertOk()->assertHeader('Cache-Control', 'no-store, private')->json('data.sso');
    expect($ticket)->not->toBeEmpty()->and($this->member->fresh()->auth_ticket)->toBe($ticket);
});

test('explicit reaction set and remove operations are safe to repeat', function () {
    $article = WebsiteArticle::create(['user_id' => $this->member->id, 'title' => 'Repeat reactions', 'short_story' => 'Story', 'full_story' => 'A story']);
    $path = '/api/v1/articles/' . $article->slug . '/reactions/heart';
    $this->actingAs($this->member)->putJson($path)->assertNoContent();
    $this->putJson($path)->assertNoContent();
    expect($article->reactions()->count())->toBe(1);
    $this->deleteJson($path)->assertNoContent();
    $this->deleteJson($path)->assertNoContent();
    expect($article->reactions()->count())->toBe(0);
});

test('an interrupted paypal request retries with its persisted provider reference', function () {
    $references = [];
    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('createOrder')->twice()->andReturnUsing(function (array $data, string $reference) use (&$references): array {
        $references[] = $reference;
        expect(data_get($data, 'purchase_units.0.custom_id'))->toBe($reference);
        if (count($references) === 1) {
            throw new RuntimeException('Provider connection interrupted');
        }

        return ['id' => 'RECOVERED-ORDER', 'links' => [['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=RECOVERED-ORDER']]];
    });
    $this->app->instance(PaypalGateway::class, $gateway);
    $this->actingAs($this->member)->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'interrupted-order'])->assertStatus(503);
    $this->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'interrupted-order'])->assertCreated();
    expect($references[0])->toBe($references[1])->and($this->member->transactions()->count())->toBe(1);
});

test('unresolved external orders remain blocked after provider retry window', function () {
    $operation = app(IdempotentOperation::class);
    try {
        $operation->execute($this->member, 'paypal-order', 'old-pending', ['amount' => 10], fn () => throw new RuntimeException('Interrupted'));
    } catch (RuntimeException) {
    }
    DB::table('website_api_idempotency_keys')->update(['created_at' => now()->subHours(7)]);
    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldNotReceive('createOrder');
    $this->app->instance(PaypalGateway::class, $gateway);
    $this->actingAs($this->member)->postJson('/api/v1/shop/paypal/orders', ['amount' => 10], ['Idempotency-Key' => 'old-pending'])->assertConflict();
    expect(WebsiteApiIdempotencyKey::query()->count())->toBe(1)->and((new WebsiteApiIdempotencyKey)->prunable()->count())->toBe(0);
});

test('ticket detail serializes persisted database timestamps', function () {
    $category = WebsiteHelpCenterCategory::create(['name' => 'Support', 'content' => 'Questions']);
    $ticket = $this->member->tickets()->create(['category_id' => $category->id, 'title' => 'Question title', 'content' => 'A detailed question', 'created_at' => '2026-09-08 12:00:00']);
    $this->actingAs($this->member)->getJson('/api/v1/support/tickets/' . $ticket->id)->assertOk()->assertJsonPath('data.created_at', '2026-09-08T12:00:00+00:00');
});

test('friend guestbook and leaderboard projections keep public presence and motto', function () {
    $friend = User::factory()->create(['rank' => 1, 'online' => '1', 'motto' => 'Public motto', 'credits' => 1000000]);
    DB::table('messenger_friendships')->insert(['user_one_id' => $this->member->id, 'user_two_id' => $friend->id, 'relation' => 0, 'friends_since' => time(), 'category' => 0]);
    $this->member->receivedHomeMessages()->create(['user_id' => $friend->id, 'content' => 'Hello']);
    app(CurrencyRepository::class)->give($friend, CurrencyTypes::Duckets, 1000000);
    DB::table('users_settings')->where('user_id', $friend->id)->update(['achievement_score' => 1000000]);
    $expected = ['id' => $friend->id, 'username' => $friend->username, 'motto' => 'Public motto', 'look' => $friend->look, 'online' => true];

    $this->actingAs($this->member)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.online_friends.0', $expected);
    foreach (['My Friends' => 'data.content.items.0', 'My Guestbook' => 'data.content.0.author'] as $name => $path) {
        $definition = HomeItem::create(['name' => $name, 'type' => HomeItemType::Widget, 'currency_type' => CurrencyTypes::Duckets, 'price' => 0, 'image' => 'widget.png']);
        $item = $this->member->homeItems()->create(['home_item_id' => $definition->id, 'placed' => true]);
        $this->getJson('/api/v1/homes/' . $this->member->username . '/widgets/' . $item->id)->assertOk()->assertJsonPath($path, $expected);
    }
    $this->getJson('/api/v1/leaderboards')->assertOk()
        ->assertJsonPath('data.credits.0.user', $expected)
        ->assertJsonPath('data.duckets.0.user', $expected)
        ->assertJsonPath('data.achievementScores.0.user', $expected);
});

test('guestbook repeat requests return the typed API rate limit without another message', function () {
    $owner = User::factory()->create(['rank' => 1]);
    $url = '/api/v1/homes/' . $owner->username . '/messages';
    $this->actingAs($this->member)->postJson($url, ['content' => 'First message'])->assertCreated();
    $this->postJson($url, ['content' => 'Second message'])->assertStatus(429)
        ->assertJsonPath('code', 'rate_limited')
        ->assertJsonPath('message', __('You are sending messages too fast.'));
    expect($owner->receivedHomeMessages()->count())->toBe(1);
});
