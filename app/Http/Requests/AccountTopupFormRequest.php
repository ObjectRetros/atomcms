<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountTopupFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1', 'max:250'],
        ];
    }
}
