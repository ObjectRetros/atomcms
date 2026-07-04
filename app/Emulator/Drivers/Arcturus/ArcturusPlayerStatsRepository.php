<?php

namespace App\Emulator\Drivers\Arcturus;

use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\Stat;
use App\Models\Game\Player\UserSetting;
use Illuminate\Support\Collection;

/**
 * Arcturus stores per-player statistics in users_settings.
 */
class ArcturusPlayerStatsRepository implements PlayerStatsRepository
{
    public function topBy(Stat $stat, int $limit, array $excludeUserIds = []): Collection
    {
        $column = $this->column($stat);

        return UserSetting::query()
            ->whereNotIn('user_id', $excludeUserIds)
            ->orderByDesc($column)
            ->limit($limit)
            ->with('user:id,username,look')
            ->get(['user_id', $column])
            ->map(fn (UserSetting $row) => $row->user === null ? null : new LeaderboardEntry($row->user, (int) $row->getAttribute($column)))
            ->filter()
            ->values();
    }

    private function column(Stat $stat): string
    {
        return match ($stat) {
            Stat::OnlineTime => 'online_time',
            Stat::RespectsReceived => 'respects_received',
            Stat::AchievementScore => 'achievement_score',
        };
    }
}
