<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmulatorSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return hasHousekeepingPermission('manage_emulator_settings');
    }

    public function view()
    {
        return hasHousekeepingPermission('manage_emulator_settings');
    }

    public function create()
    {
        return hasHousekeepingPermission('manage_emulator_settings');
    }

    public function update()
    {
        return hasHousekeepingPermission('manage_emulator_settings');
    }
}
