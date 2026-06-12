<?php

namespace App\Policies;

class WebsiteAdPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_website_ads';
    }
}
