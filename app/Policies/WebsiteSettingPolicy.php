<?php

namespace App\Policies;

class WebsiteSettingPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_website_settings';
    }

    protected function deletePermission(): string
    {
        return 'delete_website_settings';
    }
}
