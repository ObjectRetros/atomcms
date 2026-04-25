<?php

namespace App\Http\Requests\Home;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyHomeItemRequest extends FormRequest
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
        $routeUser = $this->route('user');
        $user = $routeUser instanceof User
            ? $routeUser
            : User::where('username', $routeUser)->first();

        return $user instanceof User && $this->user()?->is($user);
    }
}
