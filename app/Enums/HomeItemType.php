<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum HomeItemType: string
{
    use HasOptions;

    case Sticker = 's';
    case Note = 'n';
    case Widget = 'w';
    case Background = 'b';

    /**
     * @return array<int, string|int>
     */
    public static function valuesExcept(?HomeItemType $except = null): array
    {
        return array_filter(self::values(), fn ($value): bool => $value !== $except?->value);
    }
}
