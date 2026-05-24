<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Grid;

/** Empty fields are skipped on save so a patch-only edit doesn't trample other columns. */
class CatalogItemMassEditForm
{
    /** @return array<int, Forms\Components\Component> */
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

    /** @return array<string, int|string>  only the non-empty fields, ready for ->update() */
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
