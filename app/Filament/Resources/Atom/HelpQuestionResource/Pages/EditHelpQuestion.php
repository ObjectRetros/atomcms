<?php

namespace App\Filament\Resources\Atom\HelpQuestionResource\Pages;

use App\Filament\Resources\Atom\HelpQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHelpQuestion extends EditRecord
{
    protected static string $resource = HelpQuestionResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
