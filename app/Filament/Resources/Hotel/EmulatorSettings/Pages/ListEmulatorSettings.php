<?php

namespace App\Filament\Resources\Hotel\EmulatorSettings\Pages;

use App\Filament\Resources\Hotel\EmulatorSettings\EmulatorSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmulatorSettings extends ListRecords
{
    protected static string $resource = EmulatorSettingResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
