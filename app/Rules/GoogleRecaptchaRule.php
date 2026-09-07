<?php

namespace App\Rules;

use App\Support\OutboundHttp;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;

class GoogleRecaptchaRule implements ValidationRule
{
    public bool $implicit = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If recaptcha is disabled
        if (! (int) setting('google_recaptcha_enabled')) {
            return;
        }

        if (! is_string($value) || trim($value) === '') {
            $fail(__('The Google recaptcha must be completed'));

            return;
        }

        try {
            $response = OutboundHttp::request()
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('habbo.site.recaptcha_secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException) {
            $fail(__('The Google recaptcha could not be verified. Please try again.'));

            return;
        }

        if (! $response->successful()) {
            $fail(__('The Google recaptcha was not successful.'));

            return;
        }

        if ($response->json('success') !== true) {
            $fail(__('The Google recaptcha was not successful.'));
        }
    }
}
