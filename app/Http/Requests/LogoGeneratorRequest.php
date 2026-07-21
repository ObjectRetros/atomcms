<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoGeneratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return hasPermission('generate_logo');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,gif,webp', 'max:1024'],
        ];
    }
}
