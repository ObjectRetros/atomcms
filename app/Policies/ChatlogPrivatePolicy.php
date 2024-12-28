<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatlogPrivatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return hasHousekeepingPermission('manage_private_chatlogs');
    }
}
