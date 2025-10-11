<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;

class TwoFactorAuthenticationController extends Controller
{
    public function index(): View
    {
        return view('user.settings.two-factor');
    }

    public function store(Request $request, EnableTwoFactorAuthentication $enable): RedirectResponse
    {
        $enable($request->user());

        return redirect()->route('settings.two-factor')->with('success', __('Two-factor authentication has been enabled. Please scan the QR code to continue.'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $confirmed = $request->user()->confirmTwoFactorAuthentication($request->input('code'));
        if (! $confirmed) {
            return back()->withErrors('Invalid Two Factor Authentication code');
        }

        return redirect()->route('settings.two-factor')->with('success', __('Two-factor authentication has been confirmed.'));
    }

    public function destroy(Request $request, DisableTwoFactorAuthentication $disable): RedirectResponse
    {
        $disable($request->user());

        return redirect()->route('settings.two-factor')->with('success', __('Two-factor authentication has been disabled.'));
    }
}
