<?php

namespace App\Http\Requests\Home;

use App\Models\User;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $routeUser = $this->route('user');
        $user = $routeUser instanceof User
            ? $routeUser
            : User::where('username', $routeUser)->first();

        return $user instanceof User && ! $this->user()?->is($user);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'between:1,500', new WebsiteWordfilterRule],
        ];
    }
}
