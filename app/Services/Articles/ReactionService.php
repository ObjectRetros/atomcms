<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleReaction;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReactionService
{
    public function __construct(private readonly InteractionRateLimiter $rateLimiter) {}

    /** @return array{success: bool, added: bool, username: string} */
    public function toggleReaction(WebsiteArticle $article, User $user, string $reaction): array
    {
        $this->rateLimiter->hit($user, 'reactions', 30);

        $record = $this->toggle($article, $user, $reaction);

        return [
            'success' => true,
            'added' => (bool) $record->active,
            'username' => $user->username,
        ];
    }

    public function setReaction(WebsiteArticle $article, User $user, string $reaction, bool $active): void
    {
        Validator::make(['reaction' => $reaction], ['reaction' => ['required', Rule::in(config('habbo.reactions'))]])->validate();
        $this->rateLimiter->hit($user, 'reactions', 30);
        WebsiteArticleReaction::query()->upsert([['article_id' => $article->id, 'user_id' => $user->id, 'reaction' => $reaction, 'active' => $active]], ['article_id', 'user_id', 'reaction'], ['active']);
    }

    /**
     * Active reaction counts for an article, grouped without hydrating every
     * reaction row.
     *
     * @return Collection<string, int>
     */
    public function countsFor(WebsiteArticle $article): Collection
    {
        return $article->reactions()
            ->toBase()
            ->groupBy('reaction')
            ->selectRaw('reaction, COUNT(*) as reaction_count')
            ->pluck('reaction_count', 'reaction')
            ->map(fn ($count): int => (int) $count);
    }

    /** @return Collection<string, array<int, string>> */
    public function usersFor(WebsiteArticle $article): Collection
    {
        return $article->reactions()
            ->with('user:id,username')
            ->get()
            ->groupBy('reaction')
            ->map(fn (Collection $reactions): array => $reactions
                ->map(fn (WebsiteArticleReaction $reaction): string => $reaction->user->username ?? '')
                ->values()->all());
    }

    /**
     * The reactions the given user has active on the article.
     *
     * @return Collection<int, string>
     */
    public function reactionsFor(WebsiteArticle $article, User $user): Collection
    {
        return $article->reactions()
            ->where('user_id', $user->id)
            ->pluck('reaction')
            ->toBase();
    }

    private function toggle(WebsiteArticle $article, User $user, string $reaction): WebsiteArticleReaction
    {
        $attributes = [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'reaction' => $reaction,
        ];

        $record = WebsiteArticleReaction::query()->where($attributes)->first();

        if ($record !== null) {
            $record->update(['active' => ! $record->active]);

            return $record;
        }

        try {
            return WebsiteArticleReaction::query()->create([...$attributes, 'active' => true]);
        } catch (UniqueConstraintViolationException) {
            // A concurrent tap created the row between our check and insert;
            // the unique index is the backstop. Treat this tap as the second
            // of the two and flip the freshly created row.
            $record = WebsiteArticleReaction::query()->where($attributes)->firstOrFail();
            $record->update(['active' => ! $record->active]);

            return $record;
        }
    }
}
