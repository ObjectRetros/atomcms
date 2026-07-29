<?php

namespace App\Http\Requests;

use App\Rules\CloudflareTurnstileRule;
use App\Rules\GoogleRecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class StaffApplicationFormRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'g-recaptcha-response' => [new GoogleRecaptchaRule],
            'cf-turnstile-response' => [new CloudflareTurnstileRule],
        ];
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }
}
