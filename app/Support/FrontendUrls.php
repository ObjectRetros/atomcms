<?php

namespace App\Support;

use InvalidArgumentException;

final class FrontendUrls
{
    /**
     * @param  array<string, scalar>|array<int, scalar>  $parameters
     * @param  array<string, scalar>  $query
     */
    public function route(string $name, array $parameters = [], array $query = []): string
    {
        if (config('atom.mode') !== 'headless') {
            $path = route($name, [...$parameters, ...$query], absolute: false);

            return rtrim((string) config('app.url'), '/') . ($path === '/' ? '' : $path);
        }

        $origin = self::origin((string) config('atom.frontend_url'));
        // Route names contain dots, so retrieve them from the actual map.
        $paths = config('atom.frontend_paths', []);
        $path = $paths[$name] ?? null;

        if (! is_string($path) || ! str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\') || preg_match('/[\x00-\x20]/', $path)) {
            throw new InvalidArgumentException("Invalid frontend path for {$name}.");
        }

        $position = 0;
        $resolved = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function (array $match) use ($parameters, &$position): string {
            $value = $parameters[$match[1]] ?? $parameters[$position++] ?? null;
            if ($value === null) {
                throw new InvalidArgumentException("Missing frontend URL parameter {$match[1]}.");
            }

            return rawurlencode((string) $value);
        }, $path);

        $parts = explode('#', $origin . $resolved, 2);
        $url = $parts[0];
        if ($query !== []) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        return $url . (isset($parts[1]) ? '#' . $parts[1] : '');
    }

    public static function origin(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts) || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || empty($parts['host']) || isset($parts['user'], $parts['pass']) || isset($parts['user'])
            || isset($parts['query']) || isset($parts['fragment'])
            || ! in_array($parts['path'] ?? '', ['', '/'], true)
            || preg_match('/[\x00-\x20\\\\]/', $url)) {
            throw new InvalidArgumentException('The frontend URL must be an HTTP(S) origin without a path, credentials, query, or fragment.');
        }

        return rtrim($url, '/');
    }
}
