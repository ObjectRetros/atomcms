<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsiteTicketFormRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('website_help_center_categories', 'id')],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:65000'],
        ];
    }

    /** @return array{category_id: int, title: string, content: string} */
    public function ticketData(): array
    {
        return ['category_id' => $this->integer('category_id'), 'title' => $this->string('title')->toString(), 'content' => $this->string('content')->toString()];
    }
}
