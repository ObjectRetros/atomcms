<?php

namespace App\Policies;

class ChatlogPrivatePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_private_chatlogs';
    }
}
