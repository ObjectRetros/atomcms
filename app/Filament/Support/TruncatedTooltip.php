<?php

namespace App\Filament\Support;

use Filament\Tables\Columns\TextColumn;

final class TruncatedTooltip
{
    /**
     * Show the full column state as a tooltip, but only when the column
     * actually truncated it.
     */
    public static function of(TextColumn $column): ?string
    {
        $state = $column->getState();

        return self::when(is_string($state) ? $state : null, $column->getCharacterLimit());
    }

    /**
     * Show the full text as a tooltip when it exceeds the given limit.
     */
    public static function when(?string $text, ?int $limit): ?string
    {
        if ($text === null || $limit === null || strlen($text) <= $limit) {
            return null;
        }

        return $text;
    }
}
