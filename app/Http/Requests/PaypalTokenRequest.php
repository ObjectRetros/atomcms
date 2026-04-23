<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaypalTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }
}
