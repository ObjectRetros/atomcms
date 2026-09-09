<?php

namespace App\Services;

use App\Support\FrontendUrls;
use InvalidArgumentException;

final class OperatingMode
{
    /** @return array<string, string> */
    public function environment(string $mode, ?string $frontend, ?string $cookieDomain = null): array
    {
        if (! in_array($mode, ['full', 'headless'], true)) {
            throw new InvalidArgumentException('Mode must be full or headless.');
        }
        if ($mode === 'full') {
            return ['ATOM_MODE' => 'full'];
        }

        $frontend = FrontendUrls::origin($frontend ?? '');
        $backend = FrontendUrls::origin((string) config('app.url'));
        $frontendHost = (string) parse_url($frontend, PHP_URL_HOST);
        $backendHost = (string) parse_url($backend, PHP_URL_HOST);
        if (parse_url($backend, PHP_URL_SCHEME) !== parse_url($frontend, PHP_URL_SCHEME)) {
            throw new InvalidArgumentException('Frontend and backend must use the same scheme for browser session authentication.');
        }
        $secure = parse_url($frontend, PHP_URL_SCHEME) === 'https';
        if (! $secure && app()->environment('production')) {
            throw new InvalidArgumentException('Production browser sessions require HTTPS.');
        }
        if ($frontendHost !== $backendHost && ($cookieDomain === null || $cookieDomain === '')) {
            throw new InvalidArgumentException('Sibling hosts require an explicit --session-domain; unrelated sites require a same-origin proxy.');
        }
        $cookieDomain = ltrim($cookieDomain ?? '', '.');
        if ($cookieDomain !== '') {
            foreach ([$frontendHost, $backendHost] as $host) {
                if ($host !== $cookieDomain && ! str_ends_with($host, '.' . $cookieDomain)) {
                    throw new InvalidArgumentException('The cookie domain must contain both configured hosts.');
                }
            }
            if (! str_contains($cookieDomain, '.') || preg_match('/[\s\/:]/', $cookieDomain)) {
                throw new InvalidArgumentException('Supply a valid shared cookie domain.');
            }
        }
        $stateful = array_unique(array_map(static function (string $origin): string {
            $port = parse_url($origin, PHP_URL_PORT);

            return parse_url($origin, PHP_URL_HOST) . ($port !== null ? ':' . $port : '');
        }, [$frontend, $backend]));

        return [
            'ATOM_MODE' => 'headless',
            'ATOM_FRONTEND_URL' => $frontend,
            'ATOM_CORS_ORIGINS' => $frontend,
            'SANCTUM_STATEFUL_DOMAINS' => implode(',', $stateful),
            'SESSION_DOMAIN' => $cookieDomain,
            'SESSION_SECURE_COOKIE' => $secure ? 'true' : 'false',
        ];
    }
}
