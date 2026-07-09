<?php

namespace App\Policies;

class WebsiteBadgePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_badges';
    }
}
