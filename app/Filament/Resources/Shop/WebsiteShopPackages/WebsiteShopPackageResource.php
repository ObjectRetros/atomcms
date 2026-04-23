<?php

namespace App\Filament\Resources\Shop\WebsiteShopPackages;

use App\Models\Shop\WebsiteShopItem;
use App\Models\Shop\WebsiteShopPackage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebsiteShopPackageResource extends Resource
{
    protected static ?string $model = WebsiteShopPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|\UnitEnum|null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'shop/packages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Package Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('website_shop_category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        Textarea::make('description')
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->image()
                            ->directory('shop-packages')
                            ->visibility('public'),

                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->suffix('cents')
                            ->minValue(0),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_giftable')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Restrictions')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('min_rank')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('max_rank')
                                    ->numeric()
                                    ->minValue(0),

                                TextInput::make('limit_per_user')
                                    ->numeric()
                                    ->minValue(1),

                                TextInput::make('stock')
                                    ->numeric()
                                    ->minValue(0),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('available_from'),
                                DateTimePicker::make('available_to'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Package Items')
                    ->schema([
                        Repeater::make('packageItems')
                            ->relationship()
                            ->schema([
                                Select::make('website_shop_item_id')
                                    ->label('Item')
                                    ->options(WebsiteShopItem::query()->where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add Item'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => '$' . number_format($state / 100, 2)),

                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),

                Tables\Columns\TextColumn::make('stock')
                    ->placeholder('Unlimited'),

                Tables\Columns\IconColumn::make('is_giftable')
                    ->boolean(),

                Tables\Columns\TextColumn::make('available_from')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('available_to')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\Filter::make('available')
                    ->label('Currently Available')
                    ->query(fn (Builder $query): Builder => $query
                        ->where(fn (Builder $q) => $q
                            ->whereNull('available_from')
                            ->orWhere('available_from', '<=', now()))
                        ->where(fn (Builder $q) => $q
                            ->whereNull('available_to')
                            ->orWhere('available_to', '>=', now()))),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteShopPackages::route('/'),
            'create' => Pages\CreateWebsiteShopPackage::route('/create'),
            'edit' => Pages\EditWebsiteShopPackage::route('/{record}/edit'),
        ];
    }
}
