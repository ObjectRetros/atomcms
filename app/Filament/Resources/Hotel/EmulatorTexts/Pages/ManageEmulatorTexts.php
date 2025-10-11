<?php

namespace App\Filament\Resources\Hotel\EmulatorTexts\Pages;

use App\Filament\Resources\Hotel\EmulatorTexts\EmulatorTextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmulatorTexts extends ManageRecords
{
    protected static string $resource = EmulatorTextResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make('create'),
        ];
    }

    public function getPrimaryKey(): string
    {
        return 'key';
    }
}
