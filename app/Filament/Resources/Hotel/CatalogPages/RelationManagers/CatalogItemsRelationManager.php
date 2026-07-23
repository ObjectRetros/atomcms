<?php

namespace App\Filament\Resources\Hotel\CatalogPages\RelationManagers;

use App\Filament\Resources\Hotel\CatalogEditors\Forms\ItemBaseForm;
use App\Models\Game\Furniture\ItemBase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CatalogItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'catalogItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_ids')
                    ->label('Furniture Item')
                    ->relationship(
                        name: 'itemBase',
                        titleAttribute: 'item_name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('item_name'),
                    )
                    ->searchable()
                    ->required()
                    ->preload()
                    ->createOptionForm(ItemBaseForm::schema())
                    ->columnSpanFull(),

                TextInput::make('catalog_name')
                    ->label('Catalog Name')
                    ->required()
                    ->maxLength(100)
                    ->nullable()
                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                Grid::make(2)
                    ->schema([
                        TextInput::make('cost_credits')
                            ->label('Cost Credits')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(3),

                        TextInput::make('cost_points')
                            ->label('Cost Points')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('points_type')
                            ->label('Points Type')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(1),
                    ]),

                Grid::make(2)
                    ->schema([
                        Toggle::make('limited_stack')
                            ->label('Limited Stack')
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                        Toggle::make('limited_sells')
                            ->label('Limited Sells')
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),
                    ]),

                Grid::make(3)
                    ->schema([
                        TextInput::make('order_number')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(1),

                        TextInput::make('offer_id')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                        TextInput::make('song_id')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),
                    ]),

                Textarea::make('extradata')
                    ->label('Extra Data')
                    ->maxLength(500)
                    ->nullable()
                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                Grid::make(2)
                    ->schema([
                        Toggle::make('have_offer')
                            ->label('Have Offer')
                            ->default(true)
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                        Toggle::make('club_only')
                            ->label('Club Only')
                            ->default(false)
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('catalog_name')
            ->columns([
                ImageColumn::make('icon')
                    ->getStateUsing(fn ($record) => url($record->itemBase?->icon()))
                    ->size('25px')

                    ->label('Icon')
                    ->circular(),

                TextColumn::make('itemBase.item_name')
                    ->label('Furniture Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('catalog_name')
                    ->label('Catalog Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cost_credits')
                    ->label('Credits')
                    ->sortable(),

                TextColumn::make('cost_points')
                    ->label('Points')
                    ->sortable(),

                IconColumn::make('limited_stack')
                    ->label('Limited')
                    ->boolean(),

                IconColumn::make('club_only')
                    ->label('HC Only')
                    ->boolean(),

                TextColumn::make('itemBase.type')
                    ->label('Type')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('itemBase.width')
                    ->label('Width')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('itemBase.length')
                    ->label('Length')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('order_number')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->query(function (Builder $query, array $data): Builder {
                        return empty($data['values'])
                            ? $query
                            : $query->whereHas('itemBase', function (Builder $query) use ($data) {
                                $query->whereIn('type', $data['values']);
                            });
                    })
                    ->options(
                        fn () => ItemBase::query()
                            ->select('type')
                            ->distinct()
                            ->orderBy('type')
                            ->pluck('type', 'type')
                            ->toArray(),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('club_only')
                    ->label('HC Only'),

                TernaryFilter::make('limited_stack')
                    ->label('Limited'),
            ])
            ->defaultSort('order_number')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Edit Catalog Item'),

                Action::make('editItemBase')
                    ->label('Edit Item base')
                    ->icon('heroicon-m-cube')
                    ->modalWidth('3xl')
                    ->modalHeading('Edit Item Base')
                    ->fillForm(function ($record) {
                        $itemBase = $record->itemBase;
                        if (! $itemBase) {
                            return [];
                        }

                        return [
                            'sprite_id' => $itemBase->sprite_id,
                            'public_name' => $itemBase->public_name,
                            'item_name' => $itemBase->item_name,
                            'type' => $itemBase->type,
                            'width' => $itemBase->width,
                            'length' => $itemBase->length,
                            'stack_height' => $itemBase->stack_height,
                            'allow_stack' => $itemBase->allow_stack,
                            'allow_sit' => $itemBase->allow_sit,
                            'allow_lay' => $itemBase->allow_lay,
                            'allow_walk' => $itemBase->allow_walk,
                            'allow_gift' => $itemBase->allow_gift,
                            'allow_trade' => $itemBase->allow_trade,
                            'allow_recycle' => $itemBase->allow_recycle,
                            'allow_marketplace_sell' => $itemBase->allow_marketplace_sell,
                            'allow_inventory_stack' => $itemBase->allow_inventory_stack,
                            'interaction_type' => $itemBase->interaction_type,
                            'interaction_modes_count' => $itemBase->interaction_modes_count,
                            'vending_ids' => $itemBase->vending_ids,
                            'multiheight' => $itemBase->multiheight,
                            'customparams' => $itemBase->customparams,
                            'effect_id_male' => $itemBase->effect_id_male,
                            'effect_id_female' => $itemBase->effect_id_female,
                            'clothing_on_walk' => $itemBase->clothing_on_walk,
                        ];
                    })
                    ->schema(ItemBaseForm::schema())
                    ->action(function (array $data, $record): void {
                        // Transform any null or empty values to empty strings
                        $data = collect($data)->map(function ($value) {
                            if ($value === null || $value === '') {
                                return '';
                            }
                            if (is_bool($value)) {
                                return $value ? '1' : '0';
                            }

                            return $value;
                        })->toArray();

                        $record->itemBase->update($data);
                    })
                    ->visible(fn ($record) => $record->itemBase !== null),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
