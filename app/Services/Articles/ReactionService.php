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

        $storedReaction = WebsiteArticleReaction::toggleFor($article, $user, $reaction);

        return [
            'success' => true,
            'added' => (bool) $storedReaction->active,
            'username' => $user->username,
        ];
    }
}
