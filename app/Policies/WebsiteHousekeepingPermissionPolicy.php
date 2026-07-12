<?php

namespace App\Policies;

class WebsiteHousekeepingPermissionPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_housekeeping_permissions';
    }
}
