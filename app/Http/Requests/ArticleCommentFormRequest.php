<?php

namespace App\Http\Requests;

use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;

class ArticleCommentFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
