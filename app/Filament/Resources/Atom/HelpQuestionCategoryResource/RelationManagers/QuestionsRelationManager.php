<?php

namespace App\Filament\Resources\Atom\HelpQuestionCategoryResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Traits\TranslatableResource;
use App\Filament\Resources\Atom\HelpQuestionResource;
use Filament\Resources\RelationManagers\RelationManager;

class QuestionsRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'title';

    public static string $translateIdentifier = 'help-questions';

    protected static ?string $inverseRelationship = 'categories';

    public function form(Form $form): Form
    {
        return $form->schema(HelpQuestionResource::getForm(true));
    }

    public function table(Table $table): Table
    {
        return $table->columns(HelpQuestionResource::getTable())
            ->modifyQueryUsing(fn($query) => $query->latest())
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
