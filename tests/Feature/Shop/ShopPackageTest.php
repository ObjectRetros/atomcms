<?php

use App\Actions\Shop\PurchaseShopPackage;
use App\Data\RconResponse;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use App\Models\Shop\WebsiteShopPurchase;
use App\Models\User;
use Illuminate\Support\Collection;

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

test('the shop lists packages alongside articles', function () {
    installHotel();

    $user = User::factory()->create();
    $package = makePackage();

    $this->actingAs($user)
        ->get(route('shop.index'))
        ->assertOk()
        ->assertViewHas('shopPackages', fn ($packages) => $packages->contains($package))
        ->assertSee($package->name);
});

test('a package purchase charges the buyer and delivers its items', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 2000]);
    $package = makePackage();
    $startCredits = (int) $user->credits;

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHas('success');

    $user->refresh();

    expect((int) $user->website_balance)->toBe(1500)
        ->and((int) $user->credits)->toBe($startCredits + 200)
        ->and(WebsiteShopPurchase::where('user_id', $user->id)->count())->toBe(1);
});

test('a package price is charged exactly in minor units', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 300]);
    $package = makePackage(['price' => 299]);

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHas('success');

    expect((int) $user->refresh()->website_balance)->toBe(1)
        ->and($package->formattedPrice())->toBe('USD 2.99');
});

test('a buyer without enough balance is rejected and nothing is delivered', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 100]);
    $package = makePackage();

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHasErrors('message');

    expect((int) $user->refresh()->website_balance)->toBe(100)
        ->and(WebsiteShopPurchase::count())->toBe(0);
});

test('the per-user purchase limit is enforced', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 10000]);
    $package = makePackage(['limit_per_user' => 1]);

    $this->actingAs($user)->post(route('shop.buy-package', $package), [])->assertSessionHas('success');
    $this->actingAs($user)->post(route('shop.buy-package', $package), [])->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::where('user_id', $user->id)->count())->toBe(1)
        ->and((int) $user->refresh()->website_balance)->toBe(9500);
});

test('an out of stock package cannot be purchased', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 10000]);
    $package = makePackage(['stock' => 0]);

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::count())->toBe(0);
});

test('a non-giftable package cannot be gifted', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 10000]);
    $recipient = User::factory()->create();
    $package = makePackage(['is_giftable' => false]);

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), ['receiver' => $recipient->username])
        ->assertSessionHasErrors('message');

    expect(WebsiteShopPurchase::count())->toBe(0);
});

test('a gifted package delivers to the recipient and records the gift', function () {
    installHotel();

    $buyer = User::factory()->create(['website_balance' => 10000]);
    $recipient = User::factory()->create();
    $package = makePackage(['is_giftable' => true]);
    $recipientCredits = (int) $recipient->credits;

    $this->actingAs($buyer)
        ->post(route('shop.buy-package', $package), ['receiver' => $recipient->username])
        ->assertSessionHas('success');

    expect((int) $buyer->refresh()->website_balance)->toBe(9500)
        ->and((int) $recipient->refresh()->credits)->toBe($recipientCredits + 200)
        ->and(WebsiteShopPurchase::where('user_id', $buyer->id)->where('gifted_to', $recipient->id)->count())->toBe(1);
});

test('an online recipient is disconnected before atomic delivery', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 2000, 'online' => '1'])->refresh();
    $package = makePackage();
    $startCredits = (int) $user->credits;
    $this->rcon->connected();

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHas('success');

    expect($user->refresh()->online)->toBeFalse()
        ->and((int) $user->website_balance)->toBe(1500)
        ->and((int) $user->credits)->toBe($startCredits + 200)
        ->and(array_column($this->rcon->calls, 'method'))->toBe(['sendCommand', 'sendCommand'])
        ->and($this->rcon->calls[0]['args']['command'])->toBe('alertuser')
        ->and($this->rcon->calls[1]['args']['command'])->toBe('disconnect');
});

test('a failed online disconnect leaves balances and goods untouched', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 2000, 'online' => '1'])->refresh();
    $package = makePackage();
    $startCredits = (int) $user->credits;
    $this->rcon->connected()->respondWith(
        new RconResponse(2, 'Arcturus alert response'),
        new RconResponse(1, 'Unable to disconnect user'),
    );

    $this->actingAs($user)
        ->post(route('shop.buy-package', $package), [])
        ->assertSessionHasErrors('message');

    expect($user->refresh()->online)->toBeTrue()
        ->and((int) $user->website_balance)->toBe(2000)
        ->and((int) $user->credits)->toBe($startCredits)
        ->and(WebsiteShopPurchase::count())->toBe(0);
});

test('a fulfillment failure rolls back goods stock charge and history', function () {
    installHotel();

    $user = User::factory()->create(['website_balance' => 2000]);
    $package = makePackage(['stock' => 1]);
    $startCredits = (int) $user->credits;
    $currencies = app(CurrencyRepository::class);

    $failingCurrencies = new class($currencies) implements CurrencyRepository
    {
        public function __construct(private readonly CurrencyRepository $inner) {}

        public function balance(User $user, CurrencyTypes $currency): int
        {
            return $this->inner->balance($user, $currency);
        }

        public function give(User $user, CurrencyTypes $currency, int $amount): void
        {
            $this->inner->give($user, $currency, $amount);

            throw new RuntimeException('Simulated fulfillment failure');
        }

        public function deduct(User $user, CurrencyTypes $currency, int $amount): bool
        {
            return $this->inner->deduct($user, $currency, $amount);
        }

        public function topBy(CurrencyTypes $currency, int $limit, array $excludeUserIds = []): Collection
        {
            return $this->inner->topBy($currency, $limit, $excludeUserIds);
        }
    };

    $this->app->instance(CurrencyRepository::class, $failingCurrencies);

    expect(fn () => app(PurchaseShopPackage::class)->execute($user, $package, null))
        ->toThrow(RuntimeException::class, 'Simulated fulfillment failure');

    expect((int) $user->refresh()->website_balance)->toBe(2000)
        ->and((int) $user->credits)->toBe($startCredits)
        ->and($package->refresh()->stock)->toBe(1)
        ->and(WebsiteShopPurchase::count())->toBe(0);
});
