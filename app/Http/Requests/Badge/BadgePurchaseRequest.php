<?php

namespace App\Http\Requests\Badge;

use Illuminate\Foundation\Http\FormRequest;

class BadgePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'badge_data' => ['required', 'string'],
            'badge_name' => ['required', 'string', 'max:255'],
            'badge_description' => ['required', 'string', 'max:255'],
        ];
    }
}
