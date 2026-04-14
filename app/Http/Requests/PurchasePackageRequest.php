<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePackageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
