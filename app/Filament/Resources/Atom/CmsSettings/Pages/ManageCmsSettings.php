<?php

namespace App\Filament\Resources\Atom\CmsSettings\Pages;

use App\Filament\Resources\Atom\CmsSettings\CmsSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCmsSettings extends ManageRecords
{
    protected static string $resource = CmsSettingResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [25, 50, 100];
    }
}
