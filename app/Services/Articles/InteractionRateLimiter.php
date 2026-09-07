<?php

namespace App\Services\Articles;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class InteractionRateLimiter
{
    public function hit(User $user, string $interaction, int $maximumAttempts): void
    {
        $key = "article-interactions:{$interaction}:{$user->id}";

        if (RateLimiter::increment($key, decaySeconds: 60) > $maximumAttempts) {
            throw new TooManyRequestsHttpException(RateLimiter::availableIn($key), 'Too Many Attempts.');
        }
    }
}
