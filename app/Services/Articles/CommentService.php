<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function store(string $comment, WebsiteArticle $article): mixed
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        if ($article->userHasReachedArticleCommentLimit($user)) {
            return redirect()->back()->withErrors([
                'message' => __('You can only comment :amount times per article', ['amount' => setting('max_comment_per_article')]),
            ]);
        }

        if (! $article->can_comment) {
            return redirect()->back()->withErrors([
                'message' => __('This article has been locked from receiving comments'),
            ]);
        }

        return $article->comments()->create([
            'user_id' => $user->id,
            'comment' => $comment,
        ]);
    }

    public function destroy(WebsiteArticleComment $comment): bool|RedirectResponse|null
    {
        if (! $comment->canBeDeleted()) {
            return redirect()->back()->withErrors([
                'message' => __('You can only delete your own comments'),
            ]);
        }

        if (! $comment->delete()) {
            return redirect()->back()->withErrors([
                'message' => __('An error occurred while deleting the comment'),
            ]);
        }

        return true;
    }
}
