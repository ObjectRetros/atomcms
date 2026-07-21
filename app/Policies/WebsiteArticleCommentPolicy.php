<?php

namespace App\Policies;

use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;
use App\Services\PermissionsService;

class WebsiteArticleCommentPolicy
{
    public function __construct(private readonly PermissionsService $permissions) {}

    public function delete(User $user, WebsiteArticleComment $comment): bool
    {
        return $comment->user_id === $user->id
            || $this->permissions->allows($user, 'delete_article_comments');
    }
}
