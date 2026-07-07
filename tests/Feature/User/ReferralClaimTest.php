<?php

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
        ->get(route('claim.referral-reward'))
        ->assertSessionHas('success');

    expect($user->referrals->fresh()->referrals_total)->toBe(1)
        ->and(app(CurrencyRepository::class)->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(10);
});

test('a user without enough referrals is rejected', function () {
    $user = User::factory()->create();
    $user->referrals()->create(['referrals_total' => 1]);

    $this->actingAs($user)
        ->get(route('claim.referral-reward'))
        ->assertSessionHasErrors();

    expect($user->referrals->fresh()->referrals_total)->toBe(1);
});
