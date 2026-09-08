<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\PermissionsService;
use Illuminate\Foundation\Http\FormRequest;

class LogoGeneratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();

        return $actor instanceof User && app(PermissionsService::class)->allows($actor, 'generate_logo');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,gif,webp', 'max:1024'],
        ];
    }
}
