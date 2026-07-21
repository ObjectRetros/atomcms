<?php

namespace App\Support;

final class DiscordWebhookUrl
{
    /** @phpstan-assert-if-true string $url */
    public static function isValid(mixed $url): bool
    {
        if (! is_string($url) || strlen($url) > 2048) {
            return false;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');

        $isDiscordHost = $host === 'discord.com'
            || str_ends_with($host, '.discord.com')
            || $host === 'discordapp.com'
            || str_ends_with($host, '.discordapp.com');
        $hasForbiddenComponents = isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
            || isset($parts['query'])
            || isset($parts['fragment']);

        return ($parts['scheme'] ?? null) === 'https'
            && ! $hasForbiddenComponents
            && $isDiscordHost
            && preg_match('#^/api(?:/v\d+)?/webhooks/\d+/[A-Za-z0-9._-]+/?$#D', $path) === 1;
    }
}
