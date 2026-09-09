<?php

namespace App\Http\Requests;

use App\Services\Articles\CommentService;
use Illuminate\Foundation\Http\FormRequest;

class ArticleCommentFormRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return CommentService::rules();
    }
}
