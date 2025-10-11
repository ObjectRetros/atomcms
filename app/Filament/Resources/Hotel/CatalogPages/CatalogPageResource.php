<?php

namespace App\Filament\Resources\Hotel\CatalogPages;

use App\Filament\Resources\Hotel\CatalogPages\Pages\CreateCatalogPage;
use App\Filament\Resources\Hotel\CatalogPages\Pages\EditCatalogPage;
use App\Filament\Resources\Hotel\CatalogPages\Pages\ListCatalogPages;
use App\Filament\Resources\Hotel\CatalogPages\RelationManagers\CatalogItemsRelationManager;
use App\Models\Game\Furniture\CatalogPage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogPageResource extends Resource
{
    protected static ?string $model = CatalogPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static string $translateIdentifier = 'catalog-pages';

    protected static ?string $slug = 'hotel/catalog-pages';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('parent_id')
                    ->required()
                    ->integer(),

                TextInput::make('caption_save')
                    ->required(),

                TextInput::make('caption')
                    ->required(),

                TextInput::make('page_layout')
                    ->required(),

                TextInput::make('icon_color')
                    ->required()
                    ->integer(),

                TextInput::make('icon_image')
                    ->required()
                    ->integer(),

                TextInput::make('min_rank')
                    ->required()
                    ->integer(),

                TextInput::make('order_num')
                    ->required()
                    ->integer(),

                TextInput::make('visible')
                    ->required(),

                TextInput::make('enabled')
                    ->required(),

                TextInput::make('club_only')
                    ->required(),

                TextInput::make('vip_only')
                    ->required(),

                TextInput::make('page_headline')
                    ->required(),

                TextInput::make('page_teaser')
                    ->required(),

                TextInput::make('page_special'),

                TextInput::make('page_text1'),

                TextInput::make('page_text2'),

                TextInput::make('page_text_details'),

                TextInput::make('page_text_teaser'),

                TextInput::make('room_id')
                    ->integer(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent_id'),

                TextColumn::make('caption_save'),

                TextColumn::make('caption')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('page_layout'),

                TextColumn::make('icon_color'),

                ImageColumn::make('icon_image'),

                TextColumn::make('min_rank'),

                TextColumn::make('order_num'),

                TextColumn::make('visible'),

                TextColumn::make('enabled'),

                TextColumn::make('club_only'),

                TextColumn::make('vip_only'),

                TextColumn::make('page_headline'),

                TextColumn::make('page_teaser'),

                TextColumn::make('page_special'),

                TextColumn::make('page_text1'),

                TextColumn::make('page_text2'),

                TextColumn::make('room_id'),

                TextColumn::make('includes'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogPages::route('/'),
            'create' => CreateCatalogPage::route('/create'),
            'edit' => EditCatalogPage::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            CatalogItemsRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['caption'];
    }
}
