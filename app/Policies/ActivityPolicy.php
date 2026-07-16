<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return hasHousekeepingPermission('view_activity_logs', $user);
    }

    public function view(User $user, Activity $activity): bool
    {
        return hasHousekeepingPermission('view_activity_logs', $user);
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
