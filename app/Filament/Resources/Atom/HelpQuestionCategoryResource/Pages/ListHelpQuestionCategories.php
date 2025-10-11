<?php

namespace App\Filament\Resources\Atom\HelpQuestionCategoryResource\Pages;

use App\Filament\Resources\Atom\HelpQuestionCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHelpQuestionCategories extends ListRecords
{
    protected static string $resource = HelpQuestionCategoryResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'order';
    }
}
