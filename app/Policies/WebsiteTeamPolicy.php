<?php

namespace App\Policies;

class WebsiteTeamPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_teams';
    }
}
