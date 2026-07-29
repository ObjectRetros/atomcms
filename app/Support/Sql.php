<?php

namespace App\Support;

final class Sql
{
    /**
     * Escape LIKE wildcards so user input matches literally instead of
     * widening the pattern. Assumes the default backslash escape character.
     */
    public static function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
