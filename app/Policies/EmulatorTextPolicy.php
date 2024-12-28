<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmulatorTextPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return hasHousekeepingPermission('manage_emulator_texts');
    }

    public function view()
    {
        return hasHousekeepingPermission('manage_emulator_texts');
    }

    public function create()
    {
        return hasHousekeepingPermission('manage_emulator_texts');
    }

    public function update()
    {
        return hasHousekeepingPermission('manage_emulator_texts');
    }
}
