<?php

namespace App\Filament\Resources\User\Bans\Pages;

use App\Filament\Resources\User\Bans\BanResource;
use Filament\Resources\Pages\ManageRecords;

class ManageBans extends ManageRecords
{
    protected static string $resource = BanResource::class;

    protected function getActions(): array
    {
        return [
            // ...
        ];
    }
}
