<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class CloudflareTurnstileRule implements ValidationRule
{
    /**
     * Runs even when the field is absent, so a submission that skipped the
     * widget fails validation instead of erroring inside the verify call.
     *
     * @var bool
     */
    public $implicit = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Only enforced when enabled through website_settings.
        if (! (int) setting('cloudflare_turnstile_enabled')) {
            return;
        }

        // An enabled toggle without credentials would lock everyone out of
        // the form, so treat it as disabled and tell the operator instead.
        if (blank(config('turnstile.turnstile_secret_key'))) {
            Log::warning('Cloudflare Turnstile is enabled in website_settings but TURNSTILE_SECRET_KEY is not configured; skipping captcha verification.');

            return;
        }

        if (! is_string($value) || trim($value) === '') {
            $fail(__('Please verify that you are not a robot.'));

            return;
        }

        try {
            app(Turnstile::class)->validate($attribute, $value, $fail);
        } catch (ConnectionException) {
            $fail(__('The captcha could not be verified. Please try again.'));
        }
    }
}
