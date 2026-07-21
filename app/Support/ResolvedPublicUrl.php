<?php

namespace App\Support;

final readonly class ResolvedPublicUrl
{
    public function __construct(
        public string $url,
        public string $host,
        public int $port,
        public string $ip,
    ) {}

    public function curlResolveEntry(): string
    {
        $ip = str_contains($this->ip, ':') ? "[{$this->ip}]" : $this->ip;

        return "{$this->host}:{$this->port}:{$ip}";
    }
}
