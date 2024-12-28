<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HousekeepingPermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_housekeeping_permissions');
    }

    public function view(User $user)
    {
        return hasHousekeepingPermission('manage_housekeeping_permissions');
    }

    public function create(User $user)
    {
        return hasHousekeepingPermission('manage_housekeeping_permissions');
    }

    public function update(User $user)
    {
        return hasHousekeepingPermission('manage_housekeeping_permissions');
    }
}
