<?php

namespace App\Policies;

class WebsiteDrawBadgePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_badges';
    }
}
