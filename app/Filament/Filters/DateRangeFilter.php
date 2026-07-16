<?php

namespace App\Filament\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class DateRangeFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        $column = $name ?? throw new InvalidArgumentException('Date range filters require a column name.');

        return parent::make($name)
            ->schema([
                DatePicker::make("{$column}_from"),
                DatePicker::make("{$column}_until"),
            ])
            ->query(function (Builder $query, array $data) use ($column): Builder {
                return $query
                    ->when(
                        $data["{$column}_from"] ?? null,
                        fn (Builder $query, $date): Builder => $query->whereDate($column, '>=', $date),
                    )
                    ->when(
                        $data["{$column}_until"] ?? null,
                        fn (Builder $query, $date): Builder => $query->whereDate($column, '<=', $date),
                    );
            });
    }
}
