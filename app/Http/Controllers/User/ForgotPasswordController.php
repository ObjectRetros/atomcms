<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __invoke(): View
    {
        return view('auth.passwords.forget');
    }

    public function submitForgetPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        // Do not reveal whether the email exists, to prevent account enumeration.
        if (User::where('mail', $request->mail)->exists()) {
            $token = Str::uuid()->toString();

            PasswordResetToken::where('email', $request->mail)->delete();
            PasswordResetToken::create([
                'email' => $request->mail,
                'token' => PasswordResetToken::hashToken($token),
            ]);

            Mail::send('email.forgetPassword', ['token' => $token], function ($message) use ($request) {
                $message->to($request->mail)->subject('Reset Password');
            });
        }

        return back()->with('success', __('We have e-mailed your password reset link!'));
    }

    public function showResetPassword(string $token): View|RedirectResponse
    {
        if (! $this->validToken($token)) {
            return $this->expired();
        }

        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function submitResetPassword(ResetPasswordRequest $request, string $token): RedirectResponse
    {
        $prt = $this->validToken($token);

        if ($prt === null || $prt->user === null) {
            $prt?->delete();

            return $this->expired();
        }

        $prt->user->changePassword($request->password);
        $prt->delete();

        return to_route('login')->with('success', __('Your password has been successfully reset!'));
    }

    private function validToken(string $token): ?PasswordResetToken
    {
        $prt = PasswordResetToken::where('token', PasswordResetToken::hashToken($token))->first();

        if ($prt !== null && $prt->hasExpired()) {
            $prt->delete();

            return null;
        }

        return $prt;
    }

    private function expired(): RedirectResponse
    {
        return to_route('forgot.password.get')->withErrors(['message' => __('This token has expired!')]);
    }
}
