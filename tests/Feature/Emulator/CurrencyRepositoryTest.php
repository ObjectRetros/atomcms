<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Drivers\Arcturus\ArcturusCurrencyRepository;
use App\Emulator\Drivers\Plus\PlusCurrencyRepository;
use App\Enums\CurrencyTypes;
use App\Models\User;

/**
 * Conformance tests every currency driver must pass against its own schema.
 * The testing database carries both emulators' tables (see TestCase).
 */
dataset('currency drivers', [
    'arcturus' => [fn (): CurrencyRepository => new ArcturusCurrencyRepository],
    'plus' => [fn (): CurrencyRepository => new PlusCurrencyRepository],
]);

beforeEach(function () {
    installHotel();

    // UserObserver grants starting balances from settings on creation; zero
    // them so both drivers' assertions start from a known baseline.
    setSetting('start_duckets', '0');
    setSetting('start_diamonds', '0');
    setSetting('start_points', '0');
});

test('credits are read and written', function (CurrencyRepository $currencies) {
    $user = User::factory()->create();
    $start = $currencies->balance($user, CurrencyTypes::Credits);

    $currencies->give($user, CurrencyTypes::Credits, 100);

    expect($currencies->balance($user->fresh(), CurrencyTypes::Credits))->toBe($start + 100);
})->with('currency drivers');

test('non-credit currencies are tracked independently', function (CurrencyRepository $currencies) {
    $user = User::factory()->create();

    $currencies->give($user, CurrencyTypes::Duckets, 50);
    $currencies->give($user, CurrencyTypes::Diamonds, 5);

    expect($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(50)
        ->and($currencies->balance($user->fresh(), CurrencyTypes::Diamonds))->toBe(5);
})->with('currency drivers');

test('a negative give removes currency', function (CurrencyRepository $currencies) {
    $user = User::factory()->create();
    $currencies->give($user, CurrencyTypes::Duckets, 50);

    $currencies->give($user->fresh(), CurrencyTypes::Duckets, -20);

    expect($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(30);
})->with('currency drivers');

test('deduct is atomic and refuses to overdraw', function (CurrencyRepository $currencies) {
    $user = User::factory()->create();
    $currencies->give($user, CurrencyTypes::Duckets, 30);

    expect($currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 40))->toBeFalse()
        ->and($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(30)
        ->and($currencies->deduct($user->fresh(), CurrencyTypes::Duckets, 20))->toBeTrue()
        ->and($currencies->balance($user->fresh(), CurrencyTypes::Duckets))->toBe(10);
})->with('currency drivers');

test('the currency leaderboard ranks richer players first', function (CurrencyRepository $currencies) {
    $rich = User::factory()->create();
    $poor = User::factory()->create();

    // Amounts far above anything other tests may have left behind -
    // users_currency is MyISAM, so rows leak past transaction rollback.
    $currencies->give($rich, CurrencyTypes::Duckets, 2_000_000);
    $currencies->give($poor, CurrencyTypes::Duckets, 1_000_000);

    $ranked = $currencies->topBy(CurrencyTypes::Duckets, 2)
        ->map(fn ($entry) => $entry->user->id)
        ->all();

    expect($ranked)->toBe([$rich->id, $poor->id]);
})->with('currency drivers');
