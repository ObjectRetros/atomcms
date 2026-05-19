<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Grid;

class ItemBaseForm
{
    /** @return array<int, \Filament\Schemas\Components\Component|Forms\Components\Component> */
    public static function schema(): array
    {
        return [
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('item_name')->label('Item name')->maxLength(80)->required(),
                Forms\Components\TextInput::make('public_name')->label('Public name')->maxLength(80),
                Forms\Components\TextInput::make('sprite_id')->label('Sprite ID')->numeric()->required(),
                Forms\Components\Select::make('type')
                    ->options(['s' => 'Floor (s)', 'i' => 'Wall (i)', 'e' => 'Effect (e)', 'h' => 'Habbo (h)', 'b' => 'Badge (b)'])
                    ->native(false)
                    ->required(),
            ]),

            Grid::make(3)->schema([
                Forms\Components\TextInput::make('width')->label('Width')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('length')->label('Length')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('stack_height')->label('Stack height')->numeric()->step(0.01)->required(),
            ]),

            Grid::make(4)->schema([
                Forms\Components\Toggle::make('allow_stack')->label('Stack'),
                Forms\Components\Toggle::make('allow_sit')->label('Sit'),
                Forms\Components\Toggle::make('allow_lay')->label('Lay'),
                Forms\Components\Toggle::make('allow_walk')->label('Walk'),
                Forms\Components\Toggle::make('allow_gift')->label('Gift'),
                Forms\Components\Toggle::make('allow_trade')->label('Trade'),
                Forms\Components\Toggle::make('allow_recycle')->label('Recycle'),
                Forms\Components\Toggle::make('allow_marketplace_sell')->label('Market'),
                Forms\Components\Toggle::make('allow_inventory_stack')->label('Inv stack'),
            ]),

            Grid::make(2)->schema([
                Forms\Components\TextInput::make('interaction_type')->label('Interaction type')->maxLength(50),
                Forms\Components\TextInput::make('interaction_modes_count')->label('Modes')->numeric()->minValue(0),
            ]),

            Forms\Components\Textarea::make('vending_ids')->label('Vending IDs')->rows(2),
            Forms\Components\Textarea::make('multiheight')->label('Multi-height')->rows(2),
            Forms\Components\Textarea::make('customparams')->label('Custom params')->rows(2),

            Grid::make(2)->schema([
                Forms\Components\TextInput::make('effect_id_male')->label('Effect male')->numeric(),
                Forms\Components\TextInput::make('effect_id_female')->label('Effect female')->numeric(),
            ]),

            Forms\Components\TextInput::make('clothing_on_walk')->label('Clothing on walk')->maxLength(50),
        ];
    }
}
