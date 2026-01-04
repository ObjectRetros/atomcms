<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCatalogEditors extends ListRecords
{
    protected static string $resource = CatalogEditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
