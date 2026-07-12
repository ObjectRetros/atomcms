<?php

namespace App\Actions;

use App\Contracts\Rcon;
use App\Emulator\Contracts\BadgeRepository;
use App\Models\User;

class SendBadges
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly BadgeRepository $badges,
    ) {}

    /**
     * Grant a semicolon-separated list of badge codes, skipping owned ones.
     */
    public function execute(User $user, string $badges): void
    {
        $owned = $this->badges->codes($user);

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
        if ($this->rcon->isConnected()) {
            $this->rcon->giveBadge($user, $badge);

            return;
        }

        $this->badges->grant($user, $badge);
    }
}
