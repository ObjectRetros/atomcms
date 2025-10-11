<?php

namespace App\Filament\Resources\Atom\NavigationResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SubNavigationsRelationManager extends RelationManager
{
    protected static string $relationship = 'subNavigations';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label(__('filament::resources.inputs.label'))
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label(__('filament::resources.inputs.slug')),

                TextInput::make('order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->label(__('filament::resources.columns.order')),

                Toggle::make('visible')
                    ->label(__('filament::resources.columns.visible')),

                Toggle::make('new_tab')
                    ->label(__('filament::resources.columns.new_tab')),
            ])
            ->columns([
                'sm' => 2,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label'),

                TextColumn::make('slug')
                    ->label(__('filament::resources.columns.slug')),

                ToggleColumn::make('visible')
                    ->label(__('filament::resources.columns.visible')),

                ToggleColumn::make('new_tab')
                    ->label(__('filament::resources.columns.new_tab')),

                TextColumn::make('order')
                    ->label(__('filament::resources.columns.order')),
            ])
            ->reorderable('order')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DissociateBulkAction::make(),
                DeleteBulkAction::make(),
            ]);
    }
}
