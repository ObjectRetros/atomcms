<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function __construct(private readonly InteractionRateLimiter $rateLimiter) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return ['comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule]];
    }

    /** @return Collection<int, WebsiteArticleComment>|LengthAwarePaginator<int, WebsiteArticleComment> */
    public function forArticle(WebsiteArticle $article, bool $paginate = true): Collection|LengthAwarePaginator
    {
        $query = $article->comments()->with('user:id,username,motto,look,online')->orderBy('id');

        return $paginate ? $query->paginate(20) : $query->get();
    }

    public function store(User $user, string $comment, WebsiteArticle $article): WebsiteArticleComment
    {
        Validator::make(['comment' => $comment], self::rules())->validate();
        $this->rateLimiter->hit($user, 'comments', 10);

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
