<?php

namespace App\Policies;

class WebsiteShopCategoryPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_shop';
    }
}
