<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class AchievementPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return hasHousekeepingPermission('manage_achievements');
    }

    public function view()
    {
        return hasHousekeepingPermission('manage_achievements');
    }

    public function create()
    {
        return hasHousekeepingPermission('manage_achievements');
    }

    public function update()
    {
        return hasHousekeepingPermission('manage_achievements');
    }
}
