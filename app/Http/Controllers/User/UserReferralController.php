<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserReferralController extends Controller
{
    public function __invoke(string $referralCode): View
    {
        User::where('referral_code', '=', $referralCode)->firstOrFail();

        return view('auth.register', [
            'referral_code' => $referralCode,
        ]);
    }
}
