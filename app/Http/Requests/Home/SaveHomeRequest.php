<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class SaveHomeRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'items.*.id' => ['required', 'integer'],
            'items.*.x' => ['required', 'integer'],
            'items.*.y' => ['required', 'integer'],
            'items.*.z' => ['required', 'integer'],
            'items.*.is_reversed' => ['nullable', 'boolean'],
            'items.*.theme' => ['nullable', 'string'],
            'items.*.placed' => ['nullable', 'boolean'],
            'items.*.extra_data' => ['nullable', 'string'],
            'backgroundId' => ['required', 'integer'],
        ];
    }
}
