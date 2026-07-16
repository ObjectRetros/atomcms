<?php

namespace App\Data;

final readonly class RconResponse
{
    public function __construct(
        public int $status,
        public string $message,
    ) {}

    public function successful(): bool
    {
        return $this->status === 0;
    }
}
