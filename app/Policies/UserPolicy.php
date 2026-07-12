<?php

namespace App\Policies;

class UserPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'edit_user';
    }

    protected function deletePermission(): string
    {
        return 'delete_user';
    }
}
