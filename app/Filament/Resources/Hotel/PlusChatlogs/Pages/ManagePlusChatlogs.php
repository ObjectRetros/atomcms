<?php

namespace App\Filament\Resources\Hotel\PlusChatlogs\Pages;

use App\Filament\Resources\Hotel\PlusChatlogs\PlusChatlogResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePlusChatlogs extends ManageRecords
{
    protected static string $resource = PlusChatlogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
