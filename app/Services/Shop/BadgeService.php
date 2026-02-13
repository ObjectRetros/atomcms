<?php

namespace App\Services\Shop;

use App\Models\User;
use App\Services\RconService;

class BadgeService
{
    public function __construct(
        private readonly RconService $rcon,
    ) {}

    public function giveToUser(User $user, string $badges): void
    {
        if (empty($badges)) {
            return;
        }

        $badgeList = explode(';', $badges);
        $ownedBadges = $user->badges()->pluck('badge_code')->toArray();

        foreach ($badgeList as $badge) {
            if (in_array($badge, $ownedBadges, true)) {
                continue;
            }

            $this->giveBadge($user, $badge);
        }
    }

    private function giveBadge(User $user, string $badge): void
    {
        if ($this->rcon->isConnected) {
            $this->rcon->giveBadge($user, $badge);

            return;
        }

        $user->badges()->updateOrCreate([
            'user_id' => $user->id,
            'badge_code' => $badge,
        ]);
    }
}
