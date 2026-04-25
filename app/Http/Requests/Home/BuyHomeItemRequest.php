<?php

namespace App\Http\Requests\Home;

use Illuminate\Validation\Rule;

class BuyHomeItemRequest extends HomeRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                Rule::exists('home_items', 'id')->where('enabled', true),
            ],
            'quantity' => ['required', 'integer', 'between:1,100'],
        ];
    }

    public function authorize(): bool
    {
        return $this->isHomeOwner();
    }
}
