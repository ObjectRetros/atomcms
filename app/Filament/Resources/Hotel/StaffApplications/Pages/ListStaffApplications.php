<?php

namespace App\Filament\Resources\Hotel\StaffApplications\Pages;

use App\Filament\Resources\Hotel\StaffApplications\StaffApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaffApplications extends ListRecords
{
    protected static string $resource = StaffApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
