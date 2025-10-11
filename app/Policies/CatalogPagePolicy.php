<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogPagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('manage_catalog_pages');
    }

    public function view(User $user)
    {
        return hasHousekeepingPermission('manage_catalog_pages');
    }

    public function create(User $user)
    {
        return hasHousekeepingPermission('manage_catalog_pages');
    }

    public function update(User $user)
    {
        return hasHousekeepingPermission('manage_catalog_pages');
    }

    public function delete(User $user)
    {
        return hasHousekeepingPermission('delete_catalog_pages');
    }
}
