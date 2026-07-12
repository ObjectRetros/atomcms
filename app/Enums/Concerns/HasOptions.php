<?php

namespace App\Enums\Concerns;

/**
 * Shared helpers for backed enums exposing their cases as form options.
 */
trait HasOptions
{
    /**
     * @return array<int, string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Value => case-name map, suitable for select inputs.
     *
     * @return array<string|int, string>
     */
    public static function toInput(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name'),
        );
    }
}
