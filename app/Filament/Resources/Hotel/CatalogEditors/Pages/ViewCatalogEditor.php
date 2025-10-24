<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCatalogEditor extends ViewRecord
{
    protected static string $resource = CatalogEditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
