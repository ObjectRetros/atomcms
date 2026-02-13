<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteShopPackageResource\Pages;
use App\Filament\Resources\WebsiteShopPackageResource\RelationManagers\ItemsRelationManager;
use App\Models\WebsiteShopPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\WebsiteShopItem;

class WebsiteShopPackageResource extends Resource
{
    protected static ?string $model = WebsiteShopPackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('website_shop_category_id')
                            ->relationship('category', 'name')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->suffix('cents')
                            ->minValue(0),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_rank')
                                    ->numeric()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('max_rank')
                                    ->numeric()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('limit_per_user')
                                    ->numeric()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->minValue(0),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('available_from'),
                                Forms\Components\DateTimePicker::make('available_to'),
                            ]),
                    ]),

                Forms\Components\Section::make('Package Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('id')
                                    ->label('Item')
                                    ->options(WebsiteShopItem::query()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),

                                Forms\Components\TextInput::make('pivot.quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),

                Tables\Columns\TextColumn::make('available_from')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_to')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\Filter::make('available')
                    ->query(fn (Builder $query) => $query
                        ->where('available_from', '<=', now())
                        ->where(function ($query) {
                            $query->whereNull('available_to')
                                ->orWhere('available_to', '>=', now());
                        }))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteShopPackages::route('/'),
            'create' => Pages\CreateWebsiteShopPackage::route('/create'),
//            'view' => Pages\ViewWebsiteShopPackage::route('/{record}'),
            'edit' => Pages\EditWebsiteShopPackage::route('/{record}/edit'),
        ];
    }
}
