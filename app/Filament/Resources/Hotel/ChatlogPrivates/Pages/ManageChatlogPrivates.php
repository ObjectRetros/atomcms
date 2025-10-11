<?php

namespace App\Filament\Resources\Hotel\ChatlogPrivates\Pages;

use App\Filament\Resources\Hotel\ChatlogPrivates\ChatlogPrivateResource;
use Filament\Resources\Pages\ManageRecords;

class ManageChatlogPrivates extends ManageRecords
{
    protected static string $resource = ChatlogPrivateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
