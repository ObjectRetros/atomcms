<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CameraPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_camera_web');
    }

    public function view(User $user)
    {
        return hasHousekeepingPermission('manage_camera_web');
    }

    public function create(User $user)
    {
        return hasHousekeepingPermission('manage_camera_web');
    }

    public function update(User $user)
    {
        return hasHousekeepingPermission('manage_camera_web');
    }
}
