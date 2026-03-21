<?php

namespace App\Filament\Resources\Shop\WebsiteShopItems;

use App\Models\Shop\WebsiteShopItem;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class WebsiteShopItemResource extends Resource
{
    protected static ?string $model = WebsiteShopItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|\UnitEnum|null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'shop/items';

    public static function getFormFields(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->options([
                    'currency' => 'Currency',
                    'furniture' => 'Furniture',
                    'badge' => 'Badge',
                    'rank' => 'Rank',
                ])
                ->required()
                ->native(false),

            TextInput::make('type_value')
                ->required()
                ->maxLength(255)
                ->helperText('Currency: type:amount (e.g. credits:100). Furniture: item_id. Badge: badge_code. Rank: rank_id.'),

            FileUpload::make('image')
                ->image()
                ->directory('shop-items')
                ->visibility('public'),

            Toggle::make('is_active')
                ->default(true),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('image')
                ->square()
                ->size(40),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('type')
                ->badge()
                ->sortable(),

            Tables\Columns\TextColumn::make('type_value')
                ->label('Value'),

            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->sortable(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema(static::getFormFields())
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns(static::getTableColumns())
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Items')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'currency' => 'Currency',
                        'furniture' => 'Furniture',
                        'badge' => 'Badge',
                        'rank' => 'Rank',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
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
            'index' => Pages\ListWebsiteShopItems::route('/'),
            'create' => Pages\CreateWebsiteShopItem::route('/create'),
            'edit' => Pages\EditWebsiteShopItem::route('/{record}/edit'),
        ];
    }
}
