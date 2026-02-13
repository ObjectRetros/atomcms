<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;

class CommentService
{
    public function create(WebsiteArticle $article, User $user, string $comment): WebsiteArticleComment
    {
        return $article->comments()->create([
            'user_id' => $user->id,
            'comment' => $comment,
        ]);
    }

    public function canCreate(WebsiteArticle $article, User $user): array
    {
        if ($article->userHasReachedArticleCommentLimit()) {
            return [
                'allowed' => false,
                'message' => __('You can only comment :amount times per article', ['amount' => setting('max_comment_per_article')]),
            ];
        }

        if (! $article->can_comment) {
            return [
                'allowed' => false,
                'message' => __('This article has been locked from receiving comments'),
            ];
        }

        return ['allowed' => true];
    }

    public function delete(WebsiteArticleComment $comment, User $user): bool
    {
        if (! $comment->canBeDeleted()) {
            return false;
        }

        return $comment->delete();
    }
}
