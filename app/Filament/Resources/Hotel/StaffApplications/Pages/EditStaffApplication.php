<?php

namespace App\Filament\Resources\Hotel\StaffApplications\Pages;

use App\Filament\Resources\Hotel\StaffApplications\StaffApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffApplication extends EditRecord
{
    protected static string $resource = StaffApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
