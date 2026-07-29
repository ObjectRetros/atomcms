<?php

use App\Enums\CurrencyTypes;
use App\Enums\HomeItemType;
use App\Models\Community\RareValue\WebsiteRareValueCategory;
use App\Models\Home\HomeItem;
use App\Models\User;

beforeEach(function () {
    installHotel();
});

test('a rare value category resolves through route model binding', function () {
    $user = User::factory()->create();
    $category = WebsiteRareValueCategory::create([
        'name' => 'Thrones',
        'badge' => 'ADM',
        'priority' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('values.category', $category))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('values.category', 999999))
        ->assertNotFound();
});

test('a referral link resolves the user by referral code or 404s', function () {
    $referrer = User::factory()->create(['referral_code' => 'FRIEND-CODE']);

    $this->get(route('register.referral', 'FRIEND-CODE'))
        ->assertOk()
        ->assertViewIs('auth.register')
        ->assertViewHas('referral_code', $referrer->referral_code);

    $this->get(route('register.referral', 'NOT-A-CODE'))->assertNotFound();
});

test('widget content is scoped to the home owner and keeps its JSON 404 shape', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $widget = HomeItem::create([
        'name' => 'My Profile',
        'type' => HomeItemType::Widget,
        'currency_type' => CurrencyTypes::Duckets,
        'price' => 25,
        'image' => 'widgets/profile.png',
        'enabled' => true,
        'order' => 0,
    ]);
    $placedWidget = $owner->homeItems()->create([
        'home_item_id' => $widget->id,
        'placed' => true,
        'theme' => 'default',
    ]);

    $this->getJson(route('home.widget-content', [$owner->username, $placedWidget->id]))
        ->assertOk()
        ->assertJsonPath('name', 'My Profile');

    // The same item id under another user's home must not resolve.
    $this->getJson(route('home.widget-content', [$stranger->username, $placedWidget->id]))
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Home item not found.']);

    $this->getJson(route('home.widget-content', [$owner->username, 999999]))
        ->assertNotFound()
        ->assertExactJson(['success' => false, 'message' => 'Home item not found.']);
});
