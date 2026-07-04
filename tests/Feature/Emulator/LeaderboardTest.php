<?php

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\Stat;
use App\Enums\CurrencyTypes;
use App\Models\User;
use Illuminate\Support\Collection;

beforeEach(function () {
    installHotel();
});

test('the currency leaderboard ranks richer players first', function () {
    $currencies = app(CurrencyRepository::class);

    $rich = User::factory()->create(['username' => 'Rich', 'mail' => 'rich@example.com']);
    $poor = User::factory()->create(['username' => 'Poor', 'mail' => 'poor@example.com']);

    $currencies->give($rich, CurrencyTypes::Credits, 1_000);
    $currencies->give($poor, CurrencyTypes::Credits, -1_000);

    $ranked = $currencies->topBy(CurrencyTypes::Credits, 10)
        ->map(fn (LeaderboardEntry $entry) => $entry->user->id)
        ->all();

    expect(array_search($rich->id, $ranked, true))
        ->toBeLessThan(array_search($poor->id, $ranked, true));
});

test('the stats leaderboard query runs against the schema', function () {
    // Smoke test: exercises the driver's column mapping without seeding the
    // emulator-owned stats table.
    $top = app(PlayerStatsRepository::class)->topBy(Stat::OnlineTime, 5);

    expect($top)->toBeInstanceOf(Collection::class);
});
