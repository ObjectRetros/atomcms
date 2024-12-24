<?php

namespace App\Filament\Resources\Hotel\CatalogPageResource\RelationManagers;

use App\Models\Game\Furniture\ItemBase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class CatalogItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'catalogItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_ids')
                    ->label('Furniture Item')
                    ->relationship(
                        name: 'itemBase',
                        titleAttribute: 'item_name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('item_name')
                    )
                    ->searchable()
                    ->required()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('sprite_id')
                            ->label('Sprite ID')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('public_name')
                            ->maxLength(56),
                        Forms\Components\TextInput::make('item_name')
                            ->required()
                            ->maxLength(70),
                        Forms\Components\TextInput::make('type')
                            ->default('s')
                            ->maxLength(3),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('width')
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('length')
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('stack_height')
                                    ->numeric()
                                    ->default(0.00),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_stack')
                                    ->default(true),
                                Forms\Components\Toggle::make('allow_sit')
                                    ->default(false),
                                Forms\Components\Toggle::make('allow_lay')
                                    ->default(false),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_walk')
                                    ->default(false),
                                Forms\Components\Toggle::make('allow_gift')
                                    ->default(true),
                                Forms\Components\Toggle::make('allow_trade')
                                    ->default(true),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_recycle')
                                    ->default(false),
                                Forms\Components\Toggle::make('allow_marketplace_sell')
                                    ->default(false),
                                Forms\Components\Toggle::make('allow_inventory_stack')
                                    ->default(true),
                            ]),
                        Forms\Components\TextInput::make('interaction_type')
                            ->default('default')
                            ->maxLength(500),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('interaction_modes_count')
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('vending_ids')
                                    ->default('0')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('multiheight')
                                    ->default('0')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('customparams')
                                    ->maxLength(256),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('effect_id_male')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('effect_id_female')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Forms\Components\TextInput::make('clothing_on_walk')
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('catalog_name')
                    ->label('Catalog Name')
                    ->required()
                    ->maxLength(100)
                    ->nullable()
                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('cost_credits')
                            ->label('Cost Credits')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(3),

                        Forms\Components\TextInput::make('cost_points')
                            ->label('Cost Points')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('points_type')
                            ->label('Points Type')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(1),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('limited_stack')
                            ->label('Limited Stack')
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                        Forms\Components\Toggle::make('limited_sells')
                            ->label('Limited Sells')
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(1),

                        Forms\Components\TextInput::make('offer_id')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                        Forms\Components\TextInput::make('song_id')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state)
                            ->default(0),
                    ]),

                Forms\Components\Textarea::make('extradata')
                    ->label('Extra Data')
                    ->maxLength(500)
                    ->nullable()
                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('have_offer')
                            ->label('Have Offer')
                            ->default(true)
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0'),

                        Forms\Components\Toggle::make('club_only')
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
                Tables\Columns\ImageColumn::make('icon')
                    ->getStateUsing(fn ($record) => url($record->itemBase?->icon()))
                    ->size('25px')

                    ->label('Icon')
                    ->circular(),

                Tables\Columns\TextColumn::make('itemBase.item_name')
                    ->label('Furniture Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('catalog_name')
                    ->label('Catalog Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost_credits')
                    ->label('Credits')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_points')
                    ->label('Points')
                    ->sortable(),

                Tables\Columns\IconColumn::make('limited_stack')
                    ->label('Limited')
                    ->boolean(),

                Tables\Columns\IconColumn::make('club_only')
                    ->label('HC Only')
                    ->boolean(),

                Tables\Columns\TextColumn::make('itemBase.type')
                    ->label('Type')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('itemBase.width')
                    ->label('Width')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('itemBase.length')
                    ->label('Length')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
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
                            ->toArray()
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('club_only')
                    ->label('HC Only'),

                Tables\Filters\TernaryFilter::make('limited_stack')
                    ->label('Limited'),
            ])
            ->defaultSort('order_number')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit Catalog Item'),

                Action::make('editItemBase')
                    ->label('Edit Item base')
                    ->icon('heroicon-m-cube')
                    ->modalWidth('3xl')
                    ->modalHeading('Edit Item Base')
                    ->fillForm(function ($record) {
                        $itemBase = $record->itemBase;
                        if (!$itemBase) {
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
                    ->form([
                        Forms\Components\TextInput::make('sprite_id')
                            ->label('Sprite ID')
                            ->numeric()
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                        Forms\Components\TextInput::make('public_name')
                            ->label('Public Name')
                            ->maxLength(56)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                        Forms\Components\TextInput::make('item_name')
                            ->label('Item Name')
                            ->required()
                            ->maxLength(70),
                        Forms\Components\TextInput::make('type')
                            ->maxLength(3)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('width')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                                Forms\Components\TextInput::make('length')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                                Forms\Components\TextInput::make('stack_height')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_stack'),
                                Forms\Components\Toggle::make('allow_sit'),
                                Forms\Components\Toggle::make('allow_lay'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_walk'),
                                Forms\Components\Toggle::make('allow_gift'),
                                Forms\Components\Toggle::make('allow_trade'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('allow_recycle'),
                                Forms\Components\Toggle::make('allow_marketplace_sell'),
                                Forms\Components\Toggle::make('allow_inventory_stack'),
                            ]),
                        Forms\Components\TextInput::make('interaction_type')
                            ->maxLength(500)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('interaction_modes_count')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                                Forms\Components\TextInput::make('vending_ids')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('multiheight')
                                    ->maxLength(50)
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                                Forms\Components\TextInput::make('customparams')
                                    ->maxLength(256)
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('effect_id_male')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                                Forms\Components\TextInput::make('effect_id_female')
                                    ->numeric()
                                    ->nullable()
                                    ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                            ]),
                        Forms\Components\TextInput::make('clothing_on_walk')
                            ->maxLength(255)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => $state === null ? '' : $state),
                    ])
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

                        $record->itemBase->forceFill($data)->save();
                    })
                    ->visible(fn ($record) => $record->itemBase !== null),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
