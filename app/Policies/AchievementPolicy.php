<?php

namespace App\Policies;

class AchievementPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_achievements';
    }
}
