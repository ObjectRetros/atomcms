<?php

namespace App\Services\Articles;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleReaction;
use App\Models\User;
use Illuminate\Http\Request;

class ReactionService
{
    public function toggleReaction(WebsiteArticle $article, User $user, Request $request): array
    {
        $reaction = $request->get('reaction');

        if (! is_string($reaction) || ! in_array($reaction, config('habbo.reactions'))) {
            return ['success' => false];
        }

        $existingReaction = WebsiteArticleReaction::getReaction($article->id, $user->id, $reaction);

        if ($existingReaction) {
            $existingReaction->update(['active' => ! $existingReaction->active]);
        } else {
            $article->reactions()->create([
                'reaction' => $reaction,
            ]);
        }

        return [
            'success' => true,
            'added' => $existingReaction?->active ?? true,
            'username' => $user->username,
        ];
    }
}
