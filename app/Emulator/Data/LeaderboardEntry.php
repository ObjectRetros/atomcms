<?php

namespace App\Emulator\Data;

use App\Models\User;

/**
 * A single ranked player, normalised across emulators so the leaderboard view
 * does not care where the value came from.
 */
final class LeaderboardEntry
{
    public function __construct(
        public readonly User $user,
        public readonly int $value,
    ) {}
}
