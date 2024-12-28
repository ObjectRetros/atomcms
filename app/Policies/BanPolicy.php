<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_bans');
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Ban $ban
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return hasHousekeepingPermission('manage_bans');
    }
}
