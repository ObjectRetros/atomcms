<?php

namespace App\Policies;

class UserSettingPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'edit_user';
    }
}
