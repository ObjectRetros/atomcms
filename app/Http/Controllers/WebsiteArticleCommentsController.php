<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleCommentFormRequest;
use App\Models\WebsiteArticle;
use App\Models\WebsiteArticleComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WebsiteArticleCommentsController extends Controller
{
    public function store(WebsiteArticle $article, ArticleCommentFormRequest $request): RedirectResponse
    {
        if ($article->userHasReachedArticleCommentLimit()) {
            return redirect()->back()->withErrors([
                'message' => __('You can only comment :amount times per article', ['amount' => setting('max_comment_per_article')]),
            ]);
        }

        if (!$article->can_comment) {
            return redirect()->back()->withErrors([
                'message' => __('This article has been locked from receiving comments'),
            ]);
        }

        $article->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', __('You comment has been posted!'));
    }

    public function destroy(WebsiteArticleComment $comment): RedirectResponse
    {
        if (! $comment->canBeDeleted()) {
            return redirect()->back()->withErrors([
                'message' => __('You can only delete your own comments'),
            ]);
        }

        $comment->delete();

        return redirect()->back()->with('success', __('You comment has been deleted!'));
    }
}
