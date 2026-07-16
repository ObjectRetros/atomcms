<?php

namespace App\Support;

use Closure;
use Symfony\Component\HttpFoundation\IpUtils;

final class PublicHttpUrlResolver
{
    /** @var Closure(string): list<string> */
    private Closure $resolveHost;

    /** @param (Closure(string): list<string>)|null $resolveHost */
    public function __construct(?Closure $resolveHost = null)
    {
        $this->resolveHost = $resolveHost ?? $this->resolveDns(...);
    }

    public function resolve(string $url): ?ResolvedPublicUrl
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($url);

        if (! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || ! is_string($parts['host'] ?? null)
            || isset($parts['user'])
            || isset($parts['pass'])) {
            return null;
        }

        $host = strtolower(rtrim($parts['host'], '.'));
        $port = $parts['port'] ?? 443;

        if ($host === '' || $port !== 443) {
            return null;
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) !== false
            ? [$host]
            : ($this->resolveHost)($host);

        if ($ips === [] || collect($ips)->contains(fn (string $ip): bool => ! $this->isPublicIp($ip))) {
            return null;
        }

        return new ResolvedPublicUrl($url, $host, $port, $ips[0]);
    }

    /** @return list<string> */
    private function resolveDns(string $host): array
    {
        if (preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $host) !== 1) {
            return [];
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        if ($records === false) {
            return [];
        }

        $ips = [];

        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;

            if (is_string($ip)) {
                $ips[] = $ip;
            }
        }

        return array_values(array_unique($ips));
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_GLOBAL_RANGE) !== false
            && ! IpUtils::checkIp($ip, ['224.0.0.0/4', 'ff00::/8']);
    }
}
