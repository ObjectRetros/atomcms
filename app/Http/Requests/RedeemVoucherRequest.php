<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedeemVoucherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', Rule::exists('website_shop_vouchers', 'code')]
        ];
    }

    public function messages(): array
    {
        return [
            'code.exists' => __('It seems like the voucher code does not exist'),
        ];
    }
}