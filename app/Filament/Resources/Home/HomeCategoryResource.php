<?php

namespace App\Filament\Resources\Home;

use App\Filament\Resources\Home\HomeCategoryResource\Pages;
use App\Filament\Resources\Home\HomeCategoryResource\RelationManagers;
use App\Filament\Traits\TranslatableResource;
use App\Models\Home\HomeCategory;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Component;

class HomeCategoryResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = HomeCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Profile Management';

    protected static ?string $slug = 'home-management/categories';

    public static string $translateIdentifier = 'home-categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->label(__('filament::resources.inputs.name'))
                            ->required(),

                        TextInput::make('icon')
                            ->maxLength(255)
                            ->label(__('filament::resources.inputs.icon'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id'))
                    ->visible(fn (Component $livewire): bool => ! $livewire->isTableReordering),

                TextColumn::make('order')
                    ->label(__('filament::resources.columns.order'))
                    ->visible(fn (Component $livewire): bool => $livewire->isTableReordering),

                ImageColumn::make('icon')
                    ->disk('public')
                    ->label(__('filament::resources.columns.icon'))
                    ->size('auto'),

                TextColumn::make('name')
                    ->label(__('filament::resources.columns.name'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HomeItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeCategories::route('/'),
            'create' => Pages\CreateHomeCategory::route('/create'),
            'edit' => Pages\EditHomeCategory::route('/{record}/edit'),
        ];
    }
}
