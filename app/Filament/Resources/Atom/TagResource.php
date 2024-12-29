<?php

namespace App\Filament\Resources\Atom;

use App\Models\Articles\Tag;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Traits\TranslatableResource;
use App\Filament\Resources\Atom\TagResource\Pages;
use App\Filament\Resources\Atom\TagResource\RelationManagers;

class TagResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $slug = 'website/tags';

    public static string $translateIdentifier = 'tags';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getForm());
    }

    public static function getForm(): array
    {
        return [
            Tabs::make('Main')
                ->tabs([
                    Tab::make(__('filament::resources.tabs.Home'))
                        ->icon('heroicon-o-home')
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament::resources.inputs.name'))
                                ->required()
                                ->maxLength(255)
                                ->autocomplete()
                                ->columnSpan('full'),

                            ColorPicker::make('background_color')
                                ->label(__('filament::resources.inputs.background_color'))
                                ->required()
                                ->columnSpan('full'),
                        ]),
                ])->columnSpanFull()
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns(static::getTable())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getTable(): array
    {
        return [
            TextColumn::make('id')
                ->label(__('filament::resources.columns.id')),

            TextColumn::make('name')
                ->label(__('filament::resources.columns.name'))
                ->searchable()
                ->limit(50),

            ColorColumn::make('background_color')
                ->label(__('filament::resources.columns.background_color'))
                ->searchable()
                ->copyable()
                ->copyMessage(__('filament::resources.common.Sucessfull'))
                ->copyMessageDuration(1500)
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ArticlesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'view' => Pages\ViewTag::route('/{record}'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
