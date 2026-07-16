<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordSettingsFormRequest;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordSettingsController extends Controller
{
    public function edit(): View
    {
        return view('user.settings.password');
    }

    public function update(PasswordSettingsFormRequest $request): RedirectResponse
    {
        AuthenticatedUser::from($request)->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('settings.password.show')->with('success', __('Your password has been changed!'));
    }
}
