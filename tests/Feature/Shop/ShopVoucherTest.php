<?php

use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;

test('a shop voucher credits the balance and can only be redeemed once', function () {
    installHotel();

    $user = User::factory()->create();
    $startBalance = (int) $user->website_balance;

    WebsiteShopVoucher::create([
        'code' => 'WELCOME',
        'amount' => 5000,
        'max_uses' => 100,
        'use_count' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'WELCOME'])
        ->assertSessionHas('success');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance + 5000);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'WELCOME'])
        ->assertSessionHasErrors('message');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance + 5000)
        ->and($user->usedShopVouchers()->count())->toBe(1);
});

test('voucher redemption is throttled against brute force', function () {
    installHotel();

    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->actingAs($user)
            ->post(route('shop.use-voucher'), ['code' => 'GUESS-' . $attempt])
            ->assertSessionHasErrors('message');
    }

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'GUESS-6'])
        ->assertStatus(429);
});

test('an overlong voucher code is rejected by validation', function () {
    installHotel();

    $this->actingAs(User::factory()->create())
        ->post(route('shop.use-voucher'), ['code' => str_repeat('A', 65)])
        ->assertSessionHasErrors('code');
});

test('an expired voucher is rejected', function () {
    installHotel();

    $user = User::factory()->create();
    $startBalance = (int) $user->website_balance;

    WebsiteShopVoucher::create([
        'code' => 'EXPIRED',
        'amount' => 2500,
        'max_uses' => 100,
        'use_count' => 0,
        'expires_at' => now()->subDay(),
    ]);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'EXPIRED'])
        ->assertSessionHasErrors('message');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance);
});
