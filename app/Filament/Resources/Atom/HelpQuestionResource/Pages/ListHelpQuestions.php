<?php

namespace App\Filament\Resources\Atom\HelpQuestionResource\Pages;

use App\Filament\Resources\Atom\HelpQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHelpQuestions extends ListRecords
{
    protected static string $resource = HelpQuestionResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
