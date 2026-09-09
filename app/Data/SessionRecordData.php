<?php

namespace App\Data;

use Carbon\CarbonImmutable;

final readonly class SessionRecordData
{
    /** @param array{is_desktop: bool, platform: string|bool, browser: string|bool} $agent */
    public function __construct(public array $agent, public ?string $ipAddress, public bool $isCurrentDevice, public CarbonImmutable $lastActive) {}
}
