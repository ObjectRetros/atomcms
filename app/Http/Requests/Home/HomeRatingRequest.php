<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class HomeRatingRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function authorize(): bool
    {
        $username = $this->route('username');

        return $this->user()?->username !== $username;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
