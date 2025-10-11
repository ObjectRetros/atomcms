<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsiteSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_website_settings');
    }

    public function view(User $user)
    {
        return hasHousekeepingPermission('manage_website_settings');
    }

    public function create(User $user)
    {
        return hasHousekeepingPermission('manage_website_settings');
    }

    public function update(User $user)
    {
        return hasHousekeepingPermission('manage_website_settings');
    }

    public function delete(User $user)
    {
        return hasHousekeepingPermission('delete_website_settings');
    }
}
