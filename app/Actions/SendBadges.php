<?php

namespace App\Actions;

use App\Contracts\Rcon;
use App\Emulator\Contracts\BadgeRepository;
use App\Models\User;

final readonly class SendBadges
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly BadgeRepository $badges,
    ) {}

    /**
     * Grant a semicolon-separated list of badge codes, skipping owned ones.
     * Connectivity is probed once per call: each probe opens a TCP socket.
     */
    public function execute(User $user, string $badges): void
    {
        $owned = $this->badges->codes($user);
        $connected = $this->rcon->isConnected();

        foreach ($this->parse($badges) as $badge) {
            if (in_array($badge, $owned, true)) {
                continue;
            }

            $connected
                ? $this->rcon->giveBadge($user, $badge)
                : $this->badges->grant($user, $badge);
        }
    }

    /**
     * @return array<int, string>
     */
    private function parse(string $badges): array
    {
        return array_values(array_filter(array_map('trim', explode(';', $badges))));
    }
}
