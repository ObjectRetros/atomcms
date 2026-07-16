<?php

namespace App\Support;

use InvalidArgumentException;

final class BadgeCode
{
    public static function normalize(string $code): string
    {
        $code = trim($code);

        if (! preg_match('/\A[A-Za-z0-9_-]{1,32}\z/', $code)) {
            throw new InvalidArgumentException('Badge codes may only contain letters, numbers, underscores, and hyphens.');
        }

        return $code;
    }

    public static function filename(string $code): string
    {
        return self::normalize($code) . '.gif';
    }
}
