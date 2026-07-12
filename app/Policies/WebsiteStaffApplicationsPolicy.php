<?php

namespace App\Policies;

class WebsiteStaffApplicationsPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_staff_applications';
    }
}
