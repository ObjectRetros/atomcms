<?php

namespace App\Http\Requests;

use App\Rules\ValidateInstallationKeyRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInstallationKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'installation_key' => ['required', 'string', 'max:255', new ValidateInstallationKeyRule],
        ];
    }
}
