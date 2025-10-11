<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\InvokableRule;

class GoogleRecaptchaRule implements InvokableRule
{
    public function __invoke(string $attribute, mixed $value, Closure $fail)
    {
        // If recaptcha is disabled
        if (! (int) setting('google_recaptcha_enabled')) {
            return true;
        }

        $client = new Client;

        $response = $client->request(
            'POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => config('habbo.site.recaptcha_secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ],
            ],
        );

        if ($response->getStatusCode() !== 200) {
            $fail(__('The Google recaptcha was not successful.'));
        }

        $body = json_decode((string) $response->getBody());

        return $body->success;
    }
}
