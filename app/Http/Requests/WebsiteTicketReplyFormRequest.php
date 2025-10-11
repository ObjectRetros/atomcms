<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteTicketReplyFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:10', 'max:65000'],
        ];
    }
}
