<?php

namespace App\Http\Requests\Home;

class SaveHomeRequest extends HomeRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'items' => ['nullable', 'array', 'max:200'],
            'items.*.id' => ['required', 'integer', 'min:1'],
            'items.*.x' => ['required', 'integer', 'min:0', 'max:2000'],
            'items.*.y' => ['required', 'integer', 'min:0', 'max:2000'],
            'items.*.z' => ['required', 'integer', 'min:0', 'max:1000'],
            'items.*.is_reversed' => ['nullable', 'boolean'],
            'items.*.theme' => ['nullable', 'string', 'max:15'],
            'items.*.placed' => ['nullable', 'boolean'],
            'items.*.extra_data' => ['nullable', 'string', 'max:2000'],
            'backgroundId' => ['required', 'integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return $this->isHomeOwner();
    }
}
