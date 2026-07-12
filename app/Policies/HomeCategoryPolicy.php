<?php

namespace App\Policies;

class HomeCategoryPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_home_items';
    }
}
