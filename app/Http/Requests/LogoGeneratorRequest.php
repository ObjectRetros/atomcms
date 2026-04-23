<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoGeneratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return hasPermission('generate_logo');
    }

    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'max:2048'],
        ];
    }
}
