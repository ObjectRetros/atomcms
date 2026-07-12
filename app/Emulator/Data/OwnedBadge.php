<?php

namespace App\Emulator\Data;

/**
 * A badge a player owns, normalised across emulators. The snake_case property
 * matches what the badge widgets already render, whichever driver produced it.
 */
final class OwnedBadge
{
    public function __construct(
        public readonly string $badge_code,
        public readonly int $slot,
    ) {}
}
