<?php

namespace App\Http\Requests;

use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ArticleCommentFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule()],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            redirect()->route('article.show', $this->route('article'))->withErrors($validator)->withInput()
        );
    }
}