<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Data\Currency;
use App\Models\User;

/**
 * Conformance tests for the configured emulator's currency driver. Any new
 * driver should pass these against its own schema.
 */
beforeEach(function () {
    installHotel();
    $this->currencies = app(CurrencyRepository::class);
});

test('credits are read and written', function () {
    $user = User::factory()->create();
    $start = $this->currencies->balance($user, Currency::Credits);

    $this->currencies->give($user, Currency::Credits, 100);

    expect($this->currencies->balance($user->fresh(), Currency::Credits))->toBe($start + 100);
});

test('non-credit currencies are tracked independently', function () {
    $user = User::factory()->create();

    $this->currencies->give($user, Currency::Duckets, 50);
    $this->currencies->give($user, Currency::Diamonds, 5);

    expect($this->currencies->balance($user->fresh(), Currency::Duckets))->toBe(50)
        ->and($this->currencies->balance($user->fresh(), Currency::Diamonds))->toBe(5);
});

test('deduct is atomic and refuses to overdraw', function () {
    $user = User::factory()->create();
    $this->currencies->give($user, Currency::Duckets, 30);

    expect($this->currencies->deduct($user->fresh(), Currency::Duckets, 40))->toBeFalse()
        ->and($this->currencies->balance($user->fresh(), Currency::Duckets))->toBe(30)
        ->and($this->currencies->deduct($user->fresh(), Currency::Duckets, 20))->toBeTrue()
        ->and($this->currencies->balance($user->fresh(), Currency::Duckets))->toBe(10);
});
