<?php

namespace App\Enums;

enum HomeItemType: string
{
    case Sticker = 's';
    case Note = 'n';
    case Widget = 'w';
    case Background = 'b';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string>
     */
    public static function valuesExcept(?HomeItemType $except = null): array
    {
        return array_filter(self::values(), fn (string $value): bool => $value !== $except?->value);
    }
}
