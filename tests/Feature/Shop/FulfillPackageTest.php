<?php

use App\Actions\Shop\FulfillPackage;
use App\Emulator\Contracts\BadgeRepository;
use App\Models\Game\Furniture\Item;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\User;

function packageWithItem(string $type, string $typeValue, int $quantity = 1): WebsiteShopPackage
{
    $package = WebsiteShopPackage::create([
        'name' => "Package {$type}",
        'price' => 100,
    ]);

    $item = WebsiteShopItem::create([
        'name' => "Item {$type}",
        'type' => $type,
        'type_value' => $typeValue,
        'is_active' => true,
    ]);

    $package->items()->attach($item->id, ['quantity' => $quantity]);

    return $package;
}

test('a currency item credits the amount times the quantity', function () {
    $user = User::factory()->create();
    $startCredits = (int) $user->credits;
    $package = packageWithItem('currency', 'credits:50', quantity: 2);

    app(FulfillPackage::class)->execute($user, $package);

    expect((int) $user->refresh()->credits)->toBe($startCredits + 100);
});

test('a furniture item grants one inventory row per unit', function () {
    $user = User::factory()->create();
    $package = packageWithItem('furniture', '230', quantity: 3);

    app(FulfillPackage::class)->execute($user, $package);

    expect(Item::where('user_id', $user->id)->where('item_id', 230)->count())->toBe(3);
});

test('a badge item grants every listed badge code', function () {
    $user = User::factory()->create();
    $package = packageWithItem('badge', 'TSTA;TSTB');

    app(FulfillPackage::class)->execute($user, $package);

    expect(app(BadgeRepository::class)->codes($user))->toContain('TSTA', 'TSTB');
});

test('a rank item promotes the user', function () {
    $user = User::factory()->create(['rank' => 1]);
    $package = packageWithItem('rank', '5');

    app(FulfillPackage::class)->execute($user, $package);

    expect((int) $user->refresh()->rank)->toBe(5);
});
