<?php

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\User;

beforeEach(function () {
    installHotel();
    $this->owner = User::factory()->create();
});

function ownedHomeItem(User $owner): UserHomeItem
{
    $item = HomeItem::create([
        'name' => 'Save Sticker',
        'type' => HomeItemType::Sticker,
        'currency_type' => CurrencyTypes::Duckets,
        'price' => 5,
        'image' => 'stickers/save.png',
        'enabled' => true,
        'order' => 0,
    ]);

    return $owner->homeItems()->create([
        'home_item_id' => $item->id,
        'x' => 10,
        'y' => 10,
        'z' => 1,
        'placed' => true,
        'theme' => null,
    ]);
}

test('the home owner can save item positions', function () {
    $placed = ownedHomeItem($this->owner);

    $this->actingAs($this->owner)
        ->post(route('home.save', $this->owner->username), [
            'backgroundId' => 0,
            'items' => [
                ['id' => $placed->id, 'x' => 250, 'y' => 120, 'z' => 3, 'placed' => true],
            ],
        ])
        ->assertOk();

    $placed->refresh();

    expect($placed->x)->toBe(250)
        ->and($placed->y)->toBe(120)
        ->and($placed->z)->toBe(3);
});

test('a visitor cannot save someone else\'s home', function () {
    $placed = ownedHomeItem($this->owner);
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->post(route('home.save', $this->owner->username), [
            'backgroundId' => 0,
            'items' => [
                ['id' => $placed->id, 'x' => 999, 'y' => 999, 'z' => 9],
            ],
        ])
        ->assertForbidden();

    expect($placed->refresh()->x)->toBe(10);
});
