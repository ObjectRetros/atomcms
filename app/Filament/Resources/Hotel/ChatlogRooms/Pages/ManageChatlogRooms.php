<?php

namespace App\Filament\Resources\Hotel\ChatlogRooms\Pages;

use App\Filament\Resources\Hotel\ChatlogRooms\ChatlogRoomResource;
use Filament\Resources\Pages\ManageRecords;

class ManageChatlogRooms extends ManageRecords
{
    protected static string $resource = ChatlogRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
