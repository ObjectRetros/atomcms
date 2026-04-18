<?php

namespace App\Http\Requests\Home;

use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $username = $this->route('username');

        return $this->user()?->username !== $username;
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
