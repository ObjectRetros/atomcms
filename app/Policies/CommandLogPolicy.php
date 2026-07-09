<?php

namespace App\Policies;

class CommandLogPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_commandlogs';
    }
}
