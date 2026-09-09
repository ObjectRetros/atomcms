<?php

namespace App\Actions\Fortify\Controllers;

use App\Actions\Fortify\VerifyTwoFactorCode;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Laravel\Fortify\Http\Responses\TwoFactorLoginResponse;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     */
    public function store(TwoFactorLoginRequest $request, VerifyTwoFactorCode $verify): TwoFactorLoginResponse
    {
        $user = $request->challengedUser();

        if (! $verify($user, $request->input('code'), $request->input('recovery_code'))) {
            throw ValidationException::withMessages([
                'code' => __('Invalid Two Factor Authentication code'),
            ]);
        }

        $request->session()->forget('login.id');

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        $user->update([
            'ip_current' => $request->ip(),
        ]);

        return app(TwoFactorLoginResponse::class);
    }
}
