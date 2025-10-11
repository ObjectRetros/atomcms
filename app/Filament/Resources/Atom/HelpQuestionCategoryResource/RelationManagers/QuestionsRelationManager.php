<?php

namespace App\Filament\Resources\Atom\HelpQuestionCategoryResource\RelationManagers;

use App\Filament\Resources\Atom\HelpQuestionResource;
use App\Filament\Traits\TranslatableResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'title';

    public static string $translateIdentifier = 'help-questions';

    protected static ?string $inverseRelationship = 'categories';

    public function form(Schema $schema): Schema
    {
        return $schema->components(HelpQuestionResource::getForm(true));
    }

    public function table(Table $table): Table
    {
        return $table->columns(HelpQuestionResource::getTable())
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
