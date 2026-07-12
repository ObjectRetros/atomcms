<?php

namespace App\Http\Requests\Home;

class HomeRatingRequest extends HomeRequest
{
    public function authorize(): bool
    {
        return $this->isHomeVisitor();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
