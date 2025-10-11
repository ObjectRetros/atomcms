<?php

namespace App\Http\Requests;

use App\Rules\GoogleRecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class StaffApplicationFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];
    }
}
