<?php

use App\Actions\SendCurrency;
use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;

beforeEach(function () {
    installHotel();
    setSetting('referrals_needed', '3');
    setSetting('referral_reward_amount', '10');
    setSetting('start_diamonds', '0');
});

test('a user with enough referrals claims the reward', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 4]);

    $this->actingAs($user)
        ->post(route('claim.referral-reward'))
        ->assertSessionHas('success');

    expect($user->referrals->fresh()->referrals_total)->toBe(1)
        ->and(app(CurrencyRepository::class)->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(10)
        ->and($user->claimedReferralLog()->count())->toBe(1);
});

test('a user without enough referrals is rejected', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 1]);

    $this->actingAs($user)
        ->post(route('claim.referral-reward'))
        ->assertSessionHasErrors();

    expect($user->referrals->fresh()->referrals_total)->toBe(1);
});

test('a referral reward cannot be replayed', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 3]);

    $this->actingAs($user)
        ->post(route('claim.referral-reward'))
        ->assertSessionHas('success');

    $this->actingAs($user)
        ->post(route('claim.referral-reward'))
        ->assertSessionHasErrors();

    expect($user->referrals->fresh()->referrals_total)->toBe(0)
        ->and(app(CurrencyRepository::class)->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(10)
        ->and($user->claimedReferralLog()->count())->toBe(1);
});

test('a failed reward grant rolls back the referral claim', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 3]);

    $currency = Mockery::mock(SendCurrency::class);
    $currency->shouldReceive('execute')->once()->andThrow(new RuntimeException('Grant failed.'));
    app()->instance(SendCurrency::class, $currency);
    $this->withoutExceptionHandling();

    expect(fn () => $this->actingAs($user)->post(route('claim.referral-reward')))
        ->toThrow(RuntimeException::class, 'Grant failed.');

    expect($user->referrals->fresh()->referrals_total)->toBe(3)
        ->and($user->claimedReferralLog()->count())->toBe(0);
});

test('referral rewards cannot be claimed with a get request', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 3]);

    $this->actingAs($user)
        ->get(route('claim.referral-reward'))
        ->assertMethodNotAllowed();

    expect($user->referrals->fresh()->referrals_total)->toBe(3)
        ->and($user->claimedReferralLog()->count())->toBe(0);
});
