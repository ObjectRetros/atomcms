<?php

namespace App\Filament\Resources\Atom\Articles\RelationManagers;

use App\Filament\Resources\Atom\Tags\TagResource;
use App\Filament\Traits\TranslatableResource;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TagsRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'tags';

    protected static ?string $recordTitleAttribute = 'name';

    public static string $translateIdentifier = 'tags';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(TagResource::getTable())
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema(TagResource::getForm()),

                AttachAction::make()->preloadRecordSelect(),
            ])
            ->recordActions([
                ViewAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
