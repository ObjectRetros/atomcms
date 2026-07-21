<?php

namespace App\Support;

use InvalidArgumentException;

final class BadgeCode
{
    public const MAX_LENGTH = 64;

    public static function normalize(string $value): string
    {
        $value = trim($value);

        if (! self::isValid($value)) {
            throw new InvalidArgumentException('Badge codes must be 1 to 64 characters and may only contain letters, numbers, underscores, and hyphens.');
        }

        return $value;
    }

    public static function ensure(string $value): string
    {
        return self::normalize($value);
    }

    public static function tryNormalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        try {
            return self::normalize($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function isValid(string $value): bool
    {
        return preg_match('/\A[A-Za-z0-9_-]{1,' . self::MAX_LENGTH . '}\z/', $value) === 1;
    }

    public static function filename(string $code): string
    {
        return self::normalize($code) . '.gif';
    }
}
