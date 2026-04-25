<?php

use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeMessage;
use App\Models\Home\UserHomeRating;
use App\Models\User;

test('users cannot rate or message their own home', function () {
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

test('disabled home items are hidden from the shop and cannot be purchased', function () {
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

test('limited home item purchases are enforced when buying', function () {
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
