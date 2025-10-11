<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCommentFormRequest;
use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Services\Articles\CommentService;
use Illuminate\Http\RedirectResponse;

class WebsiteArticleCommentsController extends Controller
{
    public function __construct(public readonly CommentService $commentService) {}

    public function store(WebsiteArticle $article, ArticleCommentFormRequest $request): RedirectResponse
    {
        $this->commentService->store($request->get('comment'), $article);

        return redirect()->back()->with('success', __('You comment has been posted!'));
    }

    public function destroy(WebsiteArticleComment $comment): RedirectResponse
    {
        $this->commentService->destroy($comment);

        return redirect()->back()->with('success', __('You comment has been deleted!'));
    }
}
