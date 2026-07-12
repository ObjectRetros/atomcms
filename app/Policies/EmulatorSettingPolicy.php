<?php

namespace App\Policies;

class EmulatorSettingPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_emulator_settings';
    }
}
