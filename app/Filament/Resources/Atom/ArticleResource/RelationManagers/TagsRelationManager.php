<?php

namespace App\Filament\Resources\Atom\ArticleResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\Atom\TagResource;
use App\Filament\Traits\TranslatableResource;
use Filament\Resources\RelationManagers\RelationManager;

class TagsRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'tags';

    protected static ?string $recordTitleAttribute = 'name';

    public static string $translateIdentifier = 'tags';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
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
                Tables\Actions\CreateAction::make()
                    ->form(TagResource::getForm()),

                Tables\Actions\AttachAction::make()->preloadRecordSelect()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
