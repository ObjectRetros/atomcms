<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Forms;

use App\Models\Game\Furniture\CatalogItem;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;

class CatalogItemFullForm
{
    /** @return array<int, Component> */
    public static function schema(): array
    {
        return [
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('catalog_name')->label('Catalog name')->maxLength(255)->required(),
                Forms\Components\TextInput::make('item_ids')
                    ->label('Item IDs (items_base)')
                    ->helperText('Comma- or semicolon-separated items_base.id list. Single id is most common.')
                    ->required(),
            ]),

            Grid::make(2)->schema([
                Forms\Components\TextInput::make('page_id')->label('Page ID')->numeric()->required(),
                Forms\Components\TextInput::make('order_number')
                    ->label('Order')
                    ->numeric()
                    ->minValue(-1)
                    ->required()
                    ->helperText('-1 locks; otherwise lower = earlier.'),
            ]),

            Grid::make(3)->schema([
                Forms\Components\TextInput::make('cost_credits')->label('Credits')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('cost_points')->label('Points')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('points_type')
                    ->label('Points type')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(999)
                    ->helperText('0=duckets, 5=diamonds, 101=gotw, etc.'),
            ]),

            Grid::make(3)->schema([
                Forms\Components\TextInput::make('amount')->label('Amount')->numeric()->minValue(1)->default(1)->required(),
                Forms\Components\TextInput::make('limited_stack')
                    ->label('Limited stack')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Set > 0 to make the item LTD.'),
                Forms\Components\TextInput::make('limited_sells')
                    ->label('Limited sells')
                    ->numeric()
                    ->minValue(0)
                    ->disabled()
                    ->helperText('Auto-incremented by the emulator.'),
            ]),

            Grid::make(2)->schema([
                Forms\Components\TextInput::make('offer_id')->label('Offer ID')->numeric(),
                Forms\Components\TextInput::make('song_id')->label('Song ID')->numeric(),
            ]),

            Forms\Components\Textarea::make('extradata')->label('Extra data')->rows(2),

            Grid::make(2)->schema([
                Forms\Components\Toggle::make('have_offer')->label('Have offer'),
                Forms\Components\Toggle::make('club_only')->label('Club only'),
            ]),
        ];
    }

    public static function fillFrom(CatalogItem $record): array
    {
        return [
            'catalog_name' => $record->catalog_name,
            'item_ids' => $record->item_ids,
            'page_id' => $record->page_id,
            'order_number' => $record->order_number,
            'cost_credits' => $record->cost_credits,
            'cost_points' => $record->cost_points,
            'points_type' => $record->points_type,
            'amount' => $record->amount,
            'limited_stack' => $record->limited_stack,
            'limited_sells' => $record->limited_sells,
            'offer_id' => $record->offer_id,
            'song_id' => $record->song_id,
            'extradata' => $record->extradata,
            'have_offer' => $record->have_offer === '1',
            'club_only' => $record->club_only === '1',
        ];
    }

    public static function castForSave(array $data): array
    {
        return [
            'catalog_name' => $data['catalog_name'],
            'item_ids' => $data['item_ids'],
            'page_id' => (int) $data['page_id'],
            'order_number' => (int) $data['order_number'],
            'cost_credits' => (int) $data['cost_credits'],
            'cost_points' => (int) $data['cost_points'],
            'points_type' => $data['points_type'] !== null && $data['points_type'] !== ''
                ? (int) $data['points_type'] : null,
            'amount' => (int) $data['amount'],
            'limited_stack' => $data['limited_stack'] !== null && $data['limited_stack'] !== ''
                ? (int) $data['limited_stack'] : 0,
            'offer_id' => $data['offer_id'] !== null && $data['offer_id'] !== ''
                ? (int) $data['offer_id'] : 0,
            'song_id' => $data['song_id'] !== null && $data['song_id'] !== ''
                ? (int) $data['song_id'] : 0,
            'extradata' => (string) ($data['extradata'] ?? ''),
            'have_offer' => ! empty($data['have_offer']) ? '1' : '0',
            'club_only' => ! empty($data['club_only']) ? '1' : '0',
        ];
    }
}
