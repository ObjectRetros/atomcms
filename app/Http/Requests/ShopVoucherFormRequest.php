<?php

namespace App\Http\Requests;

use App\Rules\GoogleRecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class ShopVoucherFormRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:64'],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];
    }
}
