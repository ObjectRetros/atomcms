<?php

namespace App\Policies;

class WebsiteShopPackagePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_shop';
    }
}
