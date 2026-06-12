<?php

namespace App\Policies;

class ChatlogRoomPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_room_chatlogs';
    }
}
