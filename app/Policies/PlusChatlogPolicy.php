<?php

namespace App\Policies;

class PlusChatlogPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_room_chatlogs';
    }
}
