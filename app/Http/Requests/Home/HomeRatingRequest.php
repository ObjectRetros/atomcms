<?php

namespace App\Http\Requests\Home;

class HomeRatingRequest extends HomeRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function authorize(): bool
    {
        return $this->isHomeVisitor();
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
