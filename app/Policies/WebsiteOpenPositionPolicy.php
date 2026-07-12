<?php

namespace App\Policies;

class WebsiteOpenPositionPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_staff_applications';
    }
}
