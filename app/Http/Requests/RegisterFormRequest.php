<?php

namespace App\Http\Requests;

use App\Actions\Fortify\Rules\PasswordValidationRules;
use App\Emulator\Emulator;
use App\Rules\GoogleRecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class RegisterFormRequest extends FormRequest
{
    use PasswordValidationRules;

    protected $errorBag = 'register';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', sprintf('regex:%s', setting('username_regex')), 'max:' . Emulator::constraints()->usernameLength, Rule::unique('users')],
            'mail' => ['required', 'string', 'email', 'max:' . Emulator::constraints()->emailLength, Rule::unique('users')],
            'password' => $this->passwordRules(),
            'terms' => ['required', 'accepted'],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => __('The Google recaptcha must be completed'),
            'g-recaptcha-response.string' => __('The google recaptcha was submitted with an invalid type'),
        ];
    }
}
