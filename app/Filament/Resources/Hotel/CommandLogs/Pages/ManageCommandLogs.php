<?php

namespace App\Filament\Resources\Hotel\CommandLogs\Pages;

use App\Filament\Concerns\HasKeylessTableRecords;
use App\Filament\Resources\Hotel\CommandLogs\CommandLogResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCommandLogs extends ManageRecords
{
    use HasKeylessTableRecords;

    protected static string $resource = CommandLogResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
