<?php

namespace App\Filament\Resources\Hotel\StaffApplicationResource\Pages;

use App\Filament\Resources\Hotel\StaffApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffApplications extends ListRecords
{
    protected static string $resource = StaffApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
