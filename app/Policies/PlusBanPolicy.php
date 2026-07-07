<?php

namespace App\Policies;

class PlusBanPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_bans';
    }
}
