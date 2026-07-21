<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function store(User $user, string $comment, WebsiteArticle $article): WebsiteArticleComment
    {
        return DB::transaction(function () use ($user, $comment, $article): WebsiteArticleComment {
            $article = WebsiteArticle::whereKey($article->id)->lockForUpdate()->firstOrFail();

            if ($article->comments()->where('user_id', $user->id)->count() >= (int) setting('max_comment_per_article')) {
                throw ValidationException::withMessages([
                    'comment' => __('You can only comment :amount times per article', ['amount' => setting('max_comment_per_article')]),
                ]);
            }

            if (! $article->can_comment) {
                throw ValidationException::withMessages([
                    'comment' => __('This article has been locked from receiving comments'),
                ]);
            }

            return $article->comments()->create([
                'user_id' => $user->id,
                'comment' => $comment,
            ]);
        }, attempts: 3);
    }

    public function destroy(User $user, WebsiteArticleComment $comment): void
    {
        Gate::forUser($user)->authorize('delete', $comment);
        $comment->deleteOrFail();
    }
}
