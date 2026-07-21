<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Home\HomeItem;
use App\Models\User;

beforeEach(function () {
    installHotel();
    setSetting('start_duckets', '0');

    $this->owner = User::factory()->create();
});

function sellableHomeItem(): HomeItem
{
    return HomeItem::create([
        'name' => 'Test Sticker',
        'type' => HomeItemType::Sticker,
        'currency_type' => CurrencyTypes::Duckets,
        'price' => 25,
        'image' => 'stickers/test.png',
        'enabled' => true,
        'order' => 0,
    ]);
}

test('buying a home item charges the driver-backed balance', function () {
    $item = sellableHomeItem();
    $currencies = app(CurrencyRepository::class);
    $currencies->give($this->owner, CurrencyTypes::Duckets, 100);

    $this->actingAs($this->owner)
        ->post(route('home.buy-item', $this->owner->username), [
            'item_id' => $item->id,
            'quantity' => 2,
        ])
        ->assertOk();

    expect($currencies->balance($this->owner->fresh(), CurrencyTypes::Duckets))->toBe(50)
        ->and($this->owner->homeItems()->where('home_item_id', $item->id)->count())->toBe(2);
});

test('a buyer without enough balance is rejected', function () {
    $item = sellableHomeItem();

    $this->actingAs($this->owner)
        ->post(route('home.buy-item', $this->owner->username), [
            'item_id' => $item->id,
            'quantity' => 1,
        ])
        ->assertStatus(400);

    expect($this->owner->homeItems()->count())->toBe(0);
});

test('a visitor can rate someone\'s home', function () {
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->post(route('home.rating', $this->owner->username), ['rating' => 5])
        ->assertOk();

    expect($this->owner->homeRatings()->count())->toBe(1);
});

test('a visitor can leave a home message', function () {
    $visitor = User::factory()->create();

    $this->actingAs($visitor)
        ->post(route('home.message', $this->owner->username), ['content' => 'Cool home!'])
        ->assertOk();

    expect($this->owner->receivedHomeMessages()->count())->toBe(1);
});

test('profile widgets reflect user changes immediately', function () {
    $this->owner->update(['motto' => 'The original motto']);

    $widget = HomeItem::create([
        'name' => 'My Profile',
        'type' => HomeItemType::Widget,
        'currency_type' => CurrencyTypes::Duckets,
        'price' => 25,
        'image' => 'widgets/profile.png',
        'enabled' => true,
        'order' => 0,
    ]);
    $placedWidget = $this->owner->homeItems()->create([
        'home_item_id' => $widget->id,
        'placed' => true,
        'theme' => 'default',
    ]);

    $this->getJson(route('home.widget-content', [$this->owner->username, $placedWidget->id]))
        ->assertOk()
        ->assertJsonPath('content', fn (string $content): bool => str_contains($content, 'The original motto'));

    $this->owner->update(['motto' => 'A freshly updated motto']);

    $this->getJson(route('home.widget-content', [$this->owner->username, $placedWidget->id]))
        ->assertOk()
        ->assertJsonPath(
            'content',
            fn (string $content): bool => str_contains($content, 'A freshly updated motto')
                && ! str_contains($content, 'The original motto'),
        );
});
