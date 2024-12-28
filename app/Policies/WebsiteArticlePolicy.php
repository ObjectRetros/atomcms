<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsiteArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return hasHousekeepingPermission('write_article');
    }

    public function view(User $user)
    {
        return hasHousekeepingPermission('write_article');
    }

    public function create(User $user)
    {
        return hasHousekeepingPermission('write_article');
    }

    public function update(User $user)
    {
        return hasHousekeepingPermission('edit_article');
    }

    public function delete(User $user)
    {
        return hasHousekeepingPermission('delete_article');
    }
}
