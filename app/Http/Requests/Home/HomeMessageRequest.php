<?php

namespace App\Http\Requests\Home;

use App\Rules\WebsiteWordfilterRule;

class HomeMessageRequest extends HomeRequest
{
    public function authorize(): bool
    {
        return $this->isHomeVisitor();
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
