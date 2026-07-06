<?php

use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\Stat;
use App\Emulator\Drivers\Arcturus\ArcturusPlayerStatsRepository;
use App\Emulator\Drivers\Plus\PlusPlayerStatsRepository;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    installHotel();
});

dataset('stats drivers', [
    'arcturus' => [fn (): PlayerStatsRepository => new ArcturusPlayerStatsRepository],
    'plus' => [fn (): PlayerStatsRepository => new PlusPlayerStatsRepository],
]);

test('the stats leaderboard query runs against the schema', function (PlayerStatsRepository $stats) {
    // Smoke test: exercises the driver's column mapping without seeding the
    // emulator-owned stats table.
    expect($stats->topBy(Stat::OnlineTime, 5))->toBeInstanceOf(Collection::class);
})->with('stats drivers');

test('the plus stats leaderboard ranks players and excludes staff', function () {
    $active = User::factory()->create();
    $idle = User::factory()->create();
    $staff = User::factory()->create();

    DB::table('user_stats')->insert([
        ['id' => $active->id, 'OnlineTime' => 5_000, 'Respect' => 0, 'AchievementScore' => 0],
        ['id' => $idle->id, 'OnlineTime' => 100, 'Respect' => 0, 'AchievementScore' => 0],
        ['id' => $staff->id, 'OnlineTime' => 9_000, 'Respect' => 0, 'AchievementScore' => 0],
    ]);

    $ranked = (new PlusPlayerStatsRepository)
        ->topBy(Stat::OnlineTime, 10, [$staff->id])
        ->map(fn (LeaderboardEntry $entry) => $entry->user->id)
        ->all();

    expect($ranked)->toBe([$active->id, $idle->id]);
});

test('the leaderboard page renders with the configured driver', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('leaderboard.index'))
        ->assertOk();
});
