<?php

namespace App\Filament\Resources\Hotel;

use App\Filament\Resources\Hotel\CatalogPageResource\Pages;
use App\Filament\Resources\Hotel\CatalogPageResource\RelationManagers\CatalogItemsRelationManager;
use App\Models\Game\Furniture\CatalogPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogPageResource extends Resource
{
    protected static ?string $model = CatalogPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Hotel';

    public static string $translateIdentifier = 'catalog-pages';

    protected static ?string $slug = 'hotel/catalog-pages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatalogPages::route('/'),
            'create' => Pages\CreateCatalogPage::route('/create'),
            'edit' => Pages\EditCatalogPage::route('/{record}/edit'),
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
