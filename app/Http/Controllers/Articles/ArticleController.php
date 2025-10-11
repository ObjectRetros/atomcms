<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use App\Services\Articles\ArticleService;
use App\Services\Articles\ReactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articlesService,
        private readonly ReactionService $reactionService,
    ) {}

    public function index(): View
    {
        $articles = $this->articlesService->getArticles(true);

        return view('community.articles', [
            'articles' => $articles,
        ]);
    }

    public function show(WebsiteArticle $article): View
    {
        return view('community.article', [
            'article' => $article,
            'otherArticles' => WebsiteArticle::whereNot('slug', $article->slug)->latest('id')->take(15)->get(),
            'myReactions' => Auth::check() ? $article->reactions->where('user_id', Auth::id())->pluck('reaction') : [],
            'articleReactions' => collect($article->reactions)->groupBy('reaction', true),
        ]);
    }

    public function toggleReaction(WebsiteArticle $article, Request $request): JsonResponse
    {
        $response = $this->reactionService->toggleReaction($article, Auth::user(), $request);

        return response()->json($response);
    }
}
