<?php

namespace App\Support;

use InvalidArgumentException;

final class BadgeCode
{
    public const MAX_LENGTH = 64;

    public static function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = strtoupper(trim($value));

        return self::isValid($value) ? $value : null;
    }

    public static function ensure(string $value): string
    {
        if (! self::isValid($value)) {
            throw new InvalidArgumentException('Badge codes may contain only letters, numbers, underscores, and dashes.');
        }

        return $value;
    }

    public static function isValid(string $value): bool
    {
        return preg_match('/\A[A-Za-z0-9_-]{1,' . self::MAX_LENGTH . '}\z/', $value) === 1;
    }
}
