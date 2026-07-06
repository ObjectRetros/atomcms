<?php

namespace App\Emulator\Contracts;

use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\Stat;
use Illuminate\Support\Collection;

/**
 * Reads player statistics (online time, respects, achievement score) that each
 * emulator stores in its own way - Arcturus in users_settings, Plus in
 * user_stats, with different column names.
 */
interface PlayerStatsRepository
{
    /**
     * The highest-ranked players by a statistic, highest first.
     *
     * @param  array<int, int>  $excludeUserIds
     *
     * @return Collection<int, LeaderboardEntry>
     */
    public function topBy(Stat $stat, int $limit, array $excludeUserIds = []): Collection;
}
