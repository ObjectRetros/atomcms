<?php

namespace App\Filament\Resources\Atom\NavigationResource\Pages;

use App\Filament\Resources\Atom\NavigationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNavigations extends ListRecords
{
    protected static string $resource = NavigationResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
