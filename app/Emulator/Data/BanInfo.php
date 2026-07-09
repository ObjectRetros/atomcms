<?php

namespace App\Emulator\Data;

/**
 * An active ban, normalised across emulators. The snake_case properties match
 * what the banned page already renders, whichever driver produced it.
 */
final class BanInfo
{
    public function __construct(
        public readonly string $ban_reason,
        public readonly int $ban_expire,
    ) {}
}
