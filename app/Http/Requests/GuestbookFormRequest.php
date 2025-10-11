<?php

namespace App\Http\Requests;

use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;

class GuestbookFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:32', new WebsiteWordfilterRule],
        ];
    }
}
