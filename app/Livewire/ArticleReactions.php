<?php

namespace App\Livewire;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleReaction;
use App\Models\User;
use App\Services\Articles\ReactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ArticleReactions extends Component
{
    #[Locked]
    public WebsiteArticle $article;

    public bool $showModal = false;

    public function mount(WebsiteArticle $article): void
    {
        $this->article = $article;
    }

    public function openModal(): void
    {
        if (! Auth::check()) {
            return;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function toggleReaction(ReactionService $reactions, string $reaction): void
    {
        if (! in_array($reaction, config('habbo.reactions'), true)) {
            return;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $reactions->toggleReaction($this->article, $user, $reaction);

        $this->dispatch('reactions:loaded');
    }

    public function render(): View
    {
        // The unique (user_id, article_id, reaction) index guarantees one row
        // per user and reaction, so no PHP-side dedupe is needed.
        /** @var Collection<int, WebsiteArticleReaction> $reactions */
        $reactions = $this->article->reactions()
            ->with('user:id,username')
            ->get();

        $groupedReactions = $reactions->groupBy('reaction', true);

        $articleReactions = $groupedReactions->map(function ($group, $reaction) {
            $users = $group->map(function ($reactionItem) {
                return $reactionItem->user->username ?? '';
            })->values();

            return [
                'name' => $reaction,
                'count' => $users->count(),
                'users' => $users,
            ];
        })->values();

        $myReactions = Auth::check()
            ? $reactions->where('user_id', Auth::id())->pluck('reaction')->values()
            : collect();

        $usedReactionNames = $articleReactions->pluck('name')->values();
        $availableReactions = collect((array) config('habbo.reactions', []))
            ->reject(fn ($reaction) => $usedReactionNames->contains($reaction) || $myReactions->contains($reaction))
            ->values();

        return view('livewire.article-reactions', [
            'article' => $this->article,
            'articleReactions' => $articleReactions,
            'myReactions' => $myReactions,
            'availableReactions' => $availableReactions,
            'isAuthenticated' => Auth::check(),
        ]);
    }
}
