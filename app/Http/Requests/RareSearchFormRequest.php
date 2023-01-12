<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RareSearchFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}