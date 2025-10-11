<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wordfilter;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WordfilterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_wordfilter');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return Response|bool
     */
    public function view(User $user, Wordfilter $wordfilter)
    {
        return hasHousekeepingPermission('manage_wordfilter');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return Response|bool
     */
    public function create(User $user)
    {
        return hasHousekeepingPermission('manage_wordfilter');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return Response|bool
     */
    public function update(User $user, Wordfilter $wordfilter)
    {
        return hasHousekeepingPermission('manage_wordfilter');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return Response|bool
     */
    public function delete(User $user, Wordfilter $wordfilter)
    {
        return hasHousekeepingPermission('manage_wordfilter');
    }
}
