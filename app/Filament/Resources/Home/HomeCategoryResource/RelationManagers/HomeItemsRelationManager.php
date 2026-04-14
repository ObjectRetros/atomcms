<?php

namespace App\Filament\Resources\Home\HomeCategoryResource\RelationManagers;

use App\Filament\Resources\Home\HomeItemResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HomeItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'homeItems';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema->components(HomeItemResource::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns(HomeItemResource::getTable())
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
