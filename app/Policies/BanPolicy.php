<?php

namespace App\Policies;

class BanPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_bans';
    }
}
