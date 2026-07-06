<?php

namespace App\Policies;

class WebsiteShopItemPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_shop';
    }
}
