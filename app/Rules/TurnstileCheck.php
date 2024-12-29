<?php

namespace App\Rules;

use Closure;
use Coderflex\LaravelTurnstile\Facades\LaravelTurnstile;
use Illuminate\Contracts\Validation\ValidationRule;

class TurnstileCheck implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = LaravelTurnstile::validate($value);

        if (!$response['success'] && setting('cloudflare_turnstile_enabled')) {
            $fail(__(config('turnstile.error_messages.turnstile_check_message')));
        }
    }
}
