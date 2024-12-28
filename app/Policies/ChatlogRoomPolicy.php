<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatlogRoomPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return hasHousekeepingPermission('manage_room_chatlogs');
    }
}
