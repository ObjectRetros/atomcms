<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
    /**
     * @return Collection<int, WebsiteArticle>|LengthAwarePaginator<int, WebsiteArticle>
     */
    public function getArticles(bool $paginate = false, int $perPage = 8): Collection|LengthAwarePaginator
    {
        $query = WebsiteArticle::with(['user' => function ($query) {
            $query->select('id', 'username', 'look');
        }])->orderByDesc('id');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function fetchArticle(string $slug): WebsiteArticle
    {
        return WebsiteArticle::where('slug', '=', $slug)->firstOrFail();
    }

    /**
     * Load the relations the article page renders.
     */
    public function loadForDisplay(WebsiteArticle $article): WebsiteArticle
    {
        return $article->load(['user' => function ($query) {
            $query->select('id', 'username', 'look', 'motto', 'rank', 'hidden_staff', 'online')
                ->with('permission:id,rank_name,staff_background');
        }]);
    }

    /**
     * The latest articles shown in the sidebar next to the open article.
     *
     * @return Collection<int, WebsiteArticle>
     */
    public function otherArticles(WebsiteArticle $article, int $limit = 15): Collection
    {
        return WebsiteArticle::whereNot('slug', $article->slug)
            ->latest('id')
            ->take($limit)
            ->get();
    }
}
