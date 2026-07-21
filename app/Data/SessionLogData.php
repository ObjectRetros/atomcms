<?php

namespace App\Data;

final readonly class SessionLogData
{
    /**
     * @param  array{is_desktop: bool, platform: string|bool, browser: string|bool}  $agent
     */
    public function __construct(
        public array $agent,
        public ?string $ipAddress,
        public bool $isCurrentDevice,
        public string $lastActive,
    ) {}
}
