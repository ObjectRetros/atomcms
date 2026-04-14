<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class BuyHomeItemRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', 'exists:home_items,id'],
            'quantity' => ['required', 'integer', 'between:1,100'],
        ];
    }

    public function authorize(): bool
    {
        $username = $this->route('username');

        return $this->user()?->username === $username;
    }
}
