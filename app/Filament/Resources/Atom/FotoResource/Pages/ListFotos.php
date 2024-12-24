<?php

namespace App\Filament\Resources\Atom\FotoResource\Pages;

use App\Filament\Resources\Atom\FotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFotos extends ListRecords
{
    protected static string $resource = FotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
