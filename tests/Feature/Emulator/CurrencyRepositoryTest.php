<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Enums\CurrencyTypes;
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
    $start = $this->currencies->balance($user, CurrencyTypes::Credits);

    $this->currencies->give($user, CurrencyTypes::Credits, 100);

    expect($this->currencies->balance($user->fresh(), CurrencyTypes::Credits))->toBe($start + 100);
});

test('non-credit currencies are tracked independently', function () {
    $user = User::factory()->create();

    $this->currencies->give($user, CurrencyTypes::Duckets, 50);
    $this->currencies->give($user, CurrencyTypes::Diamonds, 5);

    expect($this->currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(50)
        ->and($this->currencies->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(5);
});

test('a negative give removes currency', function () {
    $user = User::factory()->create();
    $this->currencies->give($user, CurrencyTypes::Duckets, 50);

    $this->currencies->give($user->fresh(), CurrencyTypes::Duckets, -20);

    expect($this->currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(30);
});

test('deduct is atomic and refuses to overdraw', function () {
    $user = User::factory()->create();
    $this->currencies->give($user, CurrencyTypes::Duckets, 30);

    expect($this->currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 40))->toBeFalse()
        ->and($this->currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(30)
        ->and($this->currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 20))->toBeTrue()
        ->and($this->currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(10);
});
