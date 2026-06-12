<?php

namespace App\Policies;

class UserBadgePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'edit_user';
    }
}
