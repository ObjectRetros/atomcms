<?php

namespace App\Emulator\Support;

use App\Data\PublicUserData;
use App\Emulator\Data\LeaderboardEntry;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Turns a ranked "user id => value" map into leaderboard entries with a single
 * user lookup, so drivers that rank on their own tables never load users row
 * by row. Ranking order is preserved and missing users are dropped.
 */
final class LeaderboardEntries
{
    /**
     * @param  array<int, int>  $valuesByUserId  Highest-ranked first.
     *
     * @return Collection<int, LeaderboardEntry>
     */
    public static function forUsers(array $valuesByUserId): Collection
    {
        if ($valuesByUserId === []) {
            return collect();
        }

        $users = User::query()
            ->whereKey(array_keys($valuesByUserId))
            ->get(PublicUserData::COLUMNS)
            ->keyBy('id');

        return collect($valuesByUserId)
            ->map(function (int $value, int $userId) use ($users): ?LeaderboardEntry {
                $user = $users->get($userId);

                return $user instanceof User ? new LeaderboardEntry($user, $value) : null;
            })
            ->filter()
            ->values();
    }
}
