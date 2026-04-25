<?php

namespace App\Http\Requests\Home;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class HomeRatingRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function authorize(): bool
    {
        $routeUser = $this->route('user');
        $user = $routeUser instanceof User
            ? $routeUser
            : User::where('username', $routeUser)->first();

        return $user instanceof User && ! $this->user()?->is($user);
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
