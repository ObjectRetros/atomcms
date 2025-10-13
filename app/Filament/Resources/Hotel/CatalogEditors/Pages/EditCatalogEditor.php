<?php

namespace App\Filament\Resources\Hotel\CatalogEditors\Pages;

use App\Filament\Resources\Hotel\CatalogEditors\CatalogEditorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCatalogEditor extends EditRecord
{
    protected static string $resource = CatalogEditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
