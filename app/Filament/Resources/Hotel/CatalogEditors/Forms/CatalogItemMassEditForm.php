<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Grid;

/**
 * Mass-edit schema. Every field is nullable — empty fields skip the update so
 * the same modal can patch credits without trampling other fields.
 */
class CatalogItemMassEditForm
{
    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function schema(): array
    {
        $note = 'Leave empty to keep unchanged';

        return [
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('cost_credits')->label('Credits')->numeric()->minValue(0)->nullable()->helperText($note),
                Forms\Components\TextInput::make('cost_points')->label('Points')->numeric()->minValue(0)->nullable()->helperText($note),
                Forms\Components\TextInput::make('points_type')->label('Points type')->numeric()->minValue(0)->maxValue(999)->nullable()->helperText($note),
                Forms\Components\TextInput::make('amount')->label('Amount')->numeric()->minValue(1)->nullable()->helperText($note),
            ]),
            Forms\Components\TextInput::make('order_number')
                ->label('Order')
                ->numeric()
                ->minValue(-1)
                ->nullable()
                ->helperText($note),
            Forms\Components\Select::make('club_only')
                ->label('Club only')
                ->options(['' => '— No change —', '1' => 'Yes', '0' => 'No'])
                ->native(false)
                ->nullable()
                ->default(''),
        ];
    }

    /**
     * Reduces a submitted form payload to the columns that should actually be
     * written. Centralised so the bulk action stays one-liner.
     *
     * @return array<string, int|string>
     */
    public static function pickUpdates(array $data): array
    {
        $updates = [];

        foreach (['cost_credits', 'cost_points', 'points_type', 'amount', 'order_number'] as $col) {
            if (isset($data[$col]) && $data[$col] !== '' && $data[$col] !== null) {
                $updates[$col] = (int) $data[$col];
            }
        }

        if (! empty($data['club_only']) && in_array($data['club_only'], ['0', '1'], true)) {
            $updates['club_only'] = $data['club_only'];
        }

        return $updates;
    }
}
