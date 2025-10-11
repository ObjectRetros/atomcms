<?php

namespace App\Filament\Resources\Atom\HousekeepingPermissions\Pages;

use App\Filament\Resources\Atom\HousekeepingPermissions\HousekeepingPermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHousekeepingPermissions extends ListRecords
{
    protected static string $resource = HousekeepingPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
