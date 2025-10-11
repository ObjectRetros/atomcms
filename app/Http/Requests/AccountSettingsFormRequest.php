<?php

namespace App\Http\Requests;

use App\Rules\GoogleRecaptchaRule;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class AccountSettingsFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'string', sprintf('regex:%s', setting('username_regex')), 'min:3', 'max:25', Rule::unique('users')->ignore($this->user()->id), new WebsiteWordfilterRule],
            'mail' => ['required', 'email', Rule::unique('users')->ignore($this->user()->id), new WebsiteWordfilterRule],
            'motto' => ['nullable', 'string', 'max:127', new WebsiteWordfilterRule],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
