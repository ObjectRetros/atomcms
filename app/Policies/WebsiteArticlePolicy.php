<?php

namespace App\Policies;

use App\Models\User;

class WebsiteArticlePolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'write_article';
    }

    protected function deletePermission(): string
    {
        return 'delete_article';
    }

    public function update(User $user): bool
    {
        return hasHousekeepingPermission('edit_article');
    }
}
