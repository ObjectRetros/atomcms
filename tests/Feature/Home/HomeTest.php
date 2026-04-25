<?php

use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeMessage;
use App\Models\Home\UserHomeRating;
use App\Models\User;

test('guests can view a user home', function () {
    installHotel();

    $user = User::factory()->create([
        'username' => 'Dennis',
        'mail' => 'dennis@example.com',
    ]);

    $this->get(route('home.show', $user->username))
        ->assertOk()
        ->assertViewIs('home.show')
        ->assertViewHas('user', fn (User $viewUser): bool => $viewUser->is($user));
});

test('owners can buy and place home items', function () {
    installHotel();

    $user = User::factory()->create([
        'credits' => 100,
        'username' => 'Buyer',
        'mail' => 'buyer@example.com',
    ]);

    $item = HomeItem::create([
        'type' => 'n',
        'name' => 'Note',
        'image' => 'home-items/note.png',
        'price' => 10,
        'currency_type' => -1,
        'enabled' => true,
    ]);

    $this->actingAs($user)
        ->postJson(route('home.buy-item', $user->username), [
            'item_id' => $item->id,
            'quantity' => 1,
        ])
        ->assertOk()
        ->assertJsonPath('items.0.home_item.id', $item->id);

    $userItem = $user->homeItems()->where('home_item_id', $item->id)->firstOrFail();

    $this->actingAs($user)
        ->postJson(route('home.save', $user->username), [
            'backgroundId' => 0,
            'items' => [[
                'id' => $userItem->id,
                'x' => 120,
                'y' => 80,
                'z' => 2,
                'placed' => true,
                'is_reversed' => true,
                'theme' => 'note',
                'extra_data' => '<b>Hello home</b>',
            ]],
        ])
        ->assertOk()
        ->assertJsonPath('message', __('Home saved successfully.'));

    $userItem->refresh();

    expect($user->fresh()->credits)->toBe(90)
        ->and($item->fresh()->total_bought)->toBe(1)
        ->and($userItem->placed)->toBeTrue()
        ->and($userItem->x)->toBe(120)
        ->and($userItem->y)->toBe(80)
        ->and($userItem->z)->toBe(2)
        ->and($userItem->is_reversed)->toBeTrue()
        ->and($userItem->extra_data)->toBe('Hello home');
});

test('visitors can rate and leave messages on homes', function () {
    installHotel();

    $owner = User::factory()->create([
        'username' => 'Owner',
        'mail' => 'owner@example.com',
    ]);
    $visitor = User::factory()->create([
        'username' => 'Visitor',
        'mail' => 'visitor@example.com',
    ]);

    $this->actingAs($visitor)
        ->postJson(route('home.rating', $owner->username), ['rating' => 5])
        ->assertOk()
        ->assertJsonPath('href', route('home.show', $owner->username));

    $this->actingAs($visitor)
        ->postJson(route('home.message', $owner->username), ['content' => '<b>Nice home</b>'])
        ->assertOk()
        ->assertJsonPath('href', route('home.show', $owner->username));

    expect(UserHomeRating::query()
        ->whereBelongsTo($owner, 'ratedUser')
        ->whereBelongsTo($visitor, 'user')
        ->value('rating'))->toBe(5)
        ->and(UserHomeMessage::query()
            ->whereBelongsTo($owner, 'recipientUser')
            ->whereBelongsTo($visitor, 'user')
            ->value('content'))->toBe('Nice home');
});

test('owners cannot rate or message their own home', function () {
    installHotel();

    $user = User::factory()->create([
        'username' => 'Dennis',
        'mail' => 'dennis@example.com',
    ]);

    $this->actingAs($user)
        ->postJson(route('home.rating', $user->username), ['rating' => 5])
        ->assertForbidden();

    $this->actingAs($user)
        ->postJson(route('home.message', $user->username), ['content' => 'Nice home'])
        ->assertForbidden();

    expect(UserHomeRating::query()->count())->toBe(0)
        ->and(UserHomeMessage::query()->count())->toBe(0);
});

test('home shop only exposes enabled items', function () {
    installHotel();

    $user = User::factory()->create([
        'username' => 'Buyer',
        'mail' => 'buyer@example.com',
    ]);

    $enabledItem = HomeItem::create([
        'type' => 'n',
        'name' => 'Visible note',
        'image' => 'home-items/visible-note.png',
        'price' => 10,
        'currency_type' => -1,
        'enabled' => true,
    ]);

    $disabledItem = HomeItem::create([
        'type' => 'n',
        'name' => 'Hidden note',
        'image' => 'home-items/hidden-note.png',
        'price' => 10,
        'currency_type' => -1,
        'enabled' => false,
    ]);

    $this->actingAs($user)
        ->getJson(route('home.shop.type-items', 'notes'))
        ->assertOk()
        ->assertJsonFragment(['id' => $enabledItem->id])
        ->assertJsonMissing(['id' => $disabledItem->id]);

    $this->actingAs($user)
        ->postJson(route('home.buy-item', $user->username), [
            'item_id' => $disabledItem->id,
            'quantity' => 1,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('item_id');
});

test('home item purchase limits are enforced', function () {
    installHotel();

    $user = User::factory()->create([
        'username' => 'Buyer',
        'mail' => 'buyer@example.com',
    ]);

    $item = HomeItem::create([
        'type' => 'n',
        'name' => 'Limited note',
        'image' => 'home-items/limited-note.png',
        'price' => 10,
        'currency_type' => -1,
        'enabled' => true,
        'limit' => 1,
    ]);

    $this->actingAs($user)
        ->postJson(route('home.buy-item', $user->username), [
            'item_id' => $item->id,
            'quantity' => 1,
        ])
        ->assertOk();

    $this->actingAs($user)
        ->postJson(route('home.buy-item', $user->username), [
            'item_id' => $item->id,
            'quantity' => 1,
        ])
        ->assertBadRequest();

    expect($item->fresh()->total_bought)->toBe(1);
});
