<?php

namespace App\Policies;

class EmulatorTextPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_emulator_texts';
    }
}
