<?php

use App\Models\Shop\WebsiteShopVoucher;
use App\Models\User;

test('a shop voucher credits the balance and can only be redeemed once', function () {
    installHotel();

    $user = User::factory()->create();
    $startBalance = (int) $user->website_balance;

    WebsiteShopVoucher::create([
        'code' => 'WELCOME',
        'amount' => 50,
        'max_uses' => 100,
        'use_count' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'WELCOME'])
        ->assertSessionHas('success');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance + 50);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'WELCOME'])
        ->assertSessionHasErrors('message');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance + 50)
        ->and($user->usedShopVouchers()->count())->toBe(1);
});

test('an expired voucher is rejected', function () {
    installHotel();

    $user = User::factory()->create();
    $startBalance = (int) $user->website_balance;

    WebsiteShopVoucher::create([
        'code' => 'EXPIRED',
        'amount' => 25,
        'max_uses' => 100,
        'use_count' => 0,
        'expires_at' => now()->subDay(),
    ]);

    $this->actingAs($user)
        ->post(route('shop.use-voucher'), ['code' => 'EXPIRED'])
        ->assertSessionHasErrors('message');

    expect((int) $user->fresh()->website_balance)->toBe($startBalance);
});
