<?php

namespace App\Filament\Resources\Atom\Tags\Pages;

use App\Filament\Resources\Atom\Tags\TagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
