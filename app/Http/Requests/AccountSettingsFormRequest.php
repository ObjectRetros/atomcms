<?php

namespace App\Http\Requests;

use App\Rules\CurrentPasswordRule;
use App\Rules\GoogleRecaptchaRule;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class AccountSettingsFormRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'username' => ['sometimes', 'string', sprintf('regex:%s', setting('username_regex')), 'min:3', 'max:25', Rule::unique('users')->ignore($this->user()->id), new WebsiteWordfilterRule],
            'mail' => ['required', 'email', Rule::unique('users')->ignore($this->user()->id), new WebsiteWordfilterRule],
            'motto' => ['nullable', 'string', 'max:127', new WebsiteWordfilterRule],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];

        // Re-authenticate before a security-sensitive email change.
        if ($this->emailIsChanging()) {
            $rules['current_password'] = ['required', 'string', new CurrentPasswordRule];
        }

        return $rules;
    }

    private function emailIsChanging(): bool
    {
        return $this->user()->mail !== $this->input('mail');
    }

    public function authorize(): bool
    {
        return true;
    }
}
