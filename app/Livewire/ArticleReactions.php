<?php

namespace App\Livewire;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleReaction;
use App\Services\Articles\ReactionService;
use Illuminate\Support\Facades\Auth;
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

    public function toggleReaction(string $reaction, ReactionService $reactionService): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if (! in_array($reaction, config('habbo.reactions'), true)) {
            return;
        }

        $existingReaction = WebsiteArticleReaction::getReaction($this->article->id, $user->id, $reaction);

        if ($existingReaction) {
            $existingReaction->update(['active' => ! $existingReaction->active]);
        } else {
            $this->article->reactions()->create([
                'reaction' => $reaction,
            ]);
        }

        $this->dispatch('reactions:loaded');
    }

    public function render(): \Illuminate\View\View
    {
        $reactions = $this->article->reactions()
            ->with('user:id,username')
            ->get();

        $reactions = $reactions->unique(function ($reaction) {
            return $reaction->reaction . '-' . $reaction->user_id;
        })->values();

        $groupedReactions = $reactions->groupBy('reaction', true);

        $articleReactions = $groupedReactions->map(function ($group, $reaction) {
            $users = $group->map(function ($reactionItem) {
                return $reactionItem->user?->username ?? '';
            })->values();

            return [
                'name' => $reaction,
                'count' => $users->count(),
                'users' => $users,
            ];
        })->values();

        $myReactions = Auth::check()
            ? $reactions->where('user_id', Auth::id())->pluck('reaction')->unique()->values()
            : collect();

        $usedReactionNames = $articleReactions->pluck('name')->values();
        $availableReactions = collect(config('habbo.reactions'))
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
