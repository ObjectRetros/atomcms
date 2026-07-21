<?php

namespace App\Policies;

use App\Models\User;
use App\Services\HousekeepingPermissionsService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function __construct(private readonly HousekeepingPermissionsService $permissions) {}

    public function viewAny(User $user): bool
    {
        return $this->permissions->allows($user, 'view_activity_logs');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $this->permissions->allows($user, 'view_activity_logs');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Activity $activity): bool
    {
        return false;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return false;
    }

    public function restore(User $user, Activity $activity): bool
    {
        return false;
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return false;
    }
}
