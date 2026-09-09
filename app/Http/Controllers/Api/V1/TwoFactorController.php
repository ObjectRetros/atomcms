<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\TwoFactorSetupData;
use App\Http\Controllers\Controller;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        abort_unless($request->hasSession(), 423, 'Password confirmation required.');
        abort_if(time() - (int) $request->session()->get('auth.password_confirmed_at', 0) > (int) config('auth.password_timeout', 10800), 423, 'Password confirmation required.');
        $user = AuthenticatedUser::from($request);

        return response()->json(['data' => TwoFactorSetupData::from($user)])->header('Cache-Control', 'no-store');
    }
}
