<?php

namespace App\Filament\Resources\Atom\HelpQuestionCategoryResource\Pages;

use App\Filament\Resources\Atom\HelpQuestionCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHelpQuestionCategory extends EditRecord
{
    protected static string $resource = HelpQuestionCategoryResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
