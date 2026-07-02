<?php

namespace App\Actions;

use App\Models\User;
use App\Services\RconService;

class SendBadges
{
    public function __construct(private readonly RconService $rcon) {}

    /**
     * Grant a semicolon-separated list of badge codes, skipping owned ones.
     */
    public function execute(User $user, string $badges): void
    {
        $owned = $user->badges()->pluck('badge_code')->all();

        foreach ($this->parse($badges) as $badge) {
            if (in_array($badge, $owned, true)) {
                continue;
            }

            $this->grant($user, $badge);
        }
    }

    /**
     * @return array<int, string>
     */
    private function parse(string $badges): array
    {
        return array_values(array_filter(array_map('trim', explode(';', $badges))));
    }

    private function grant(User $user, string $badge): void
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
