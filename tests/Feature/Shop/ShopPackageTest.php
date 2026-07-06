<?php

use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\Shop\WebsiteShopPurchase;
use App\Models\User;

function makePackage(array $attributes = []): WebsiteShopPackage
{
    $package = WebsiteShopPackage::create([
        'name' => 'Starter Bundle',
        'price' => 500, // $5.00
        ...$attributes,
    ]);

    $credits = WebsiteShopItem::create([
        'name' => '100 Credits',
        'type' => 'currency',
        'type_value' => 'credits:100',
        'is_active' => true,
    ]);

    $package->items()->attach($credits->id, ['quantity' => 2]);

    return $package;
}

test('a package purchase charges the buyer and delivers its items', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 20]);
    $package = makePackage();
    $startCredits = (int) $user->credits;

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHas('success');

    $user->refresh();

    expect((float) $user->website_balance)->toBe(15.0)
        ->and((int) $user->credits)->toBe($startCredits + 200)
        ->and(WebsiteShopPurchase::where('user_id', $user->id)->count())->toBe(1);
});

test('a buyer without enough balance is rejected and nothing is delivered', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 1]);
    $package = makePackage();

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHasErrors('message');

    expect((float) $user->refresh()->website_balance)->toBe(1.0)
        ->and(WebsiteShopPurchase::count())->toBe(0);
});

test('the per-user purchase limit is enforced', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 100]);
    $package = makePackage(['limit_per_user' => 1]);

    $this->actingAs($user)->post(route('shop.buy-package', $package), [])->assertSessionHas('success');
    $this->actingAs($user)->post(route('shop.buy-package', $package), [])->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::where('user_id', $user->id)->count())->toBe(1)
        ->and((float) $user->refresh()->website_balance)->toBe(95.0);
});

test('an out of stock package cannot be purchased', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 100]);
    $package = makePackage(['stock' => 0]);

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::count())->toBe(0);
});

test('a non-giftable package cannot be gifted', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 100]);
    $recipient = User::factory()->create();
    $package = makePackage(['is_giftable' => false]);

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), ['receiver' => $recipient->username])
        ->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::count())->toBe(0);
});

test('a gifted package delivers to the recipient and records the gift', function () {
    installHotel();

    $buyer = User::factory()->create(['website_balance' => 100]);
    $recipient = User::factory()->create();
    $package = makePackage(['is_giftable' => true]);
    $recipientCredits = (int) $recipient->credits;

    $this->actingAs($buyer)
        ->post(route('shop.buy-package', $package), ['receiver' => $recipient->username])
        ->assertSessionHas('success');

    expect((float) $buyer->refresh()->website_balance)->toBe(95.0)
        ->and((int) $recipient->refresh()->credits)->toBe($recipientCredits + 200)
        ->and(WebsiteShopPurchase::where('user_id', $buyer->id)->where('gifted_to', $recipient->id)->count())->toBe(1);
});
