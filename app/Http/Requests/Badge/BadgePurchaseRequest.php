<?php

namespace App\Http\Requests\Badge;

use App\Rules\ValidGifBadge;
use Illuminate\Foundation\Http\FormRequest;

class BadgePurchaseRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'badge_data' => ['required', 'string', new ValidGifBadge],
            'badge_name' => ['required', 'string', 'max:255'],
            'badge_description' => ['required', 'string', 'max:255'],
        ];
    }
}
