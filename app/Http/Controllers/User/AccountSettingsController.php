<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Services\RconService;
use App\Services\User\SessionService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class AccountSettingsController extends Controller
{
    public function __construct(private readonly SessionService $sessionService, private readonly UserService $userService, private readonly RconService $rconService)
    {
    }

    public function edit(): View
    {
        return view('user.settings.account', [
            'user' => Auth::user()->load('settings:allow_name_change'),
        ]);
    }

    public function sessionLogs(Request $request): View
    {
        $sessions = $this->sessionService->fetchSessionLogs($request);

        return view('user.settings.session-logs', [
            'logs' => $sessions,
        ]);
    }

    public function update(AccountSettingsFormRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->back()->withErrors('User not found');
        }

        $allowedNameChange = $user->settings?->allow_name_change && $user->username !== $request->input('username');

        if (!$this->rconService->isConnected() && Auth::user()->online === '1') {
            return back()->withErrors('You must be offline to change your account settings');
        }

        if ($allowedNameChange) {
            $this->rconService->disconnectUser($user);
            $this->userService->updateField($user, 'username', $request->input('username'));
        }

        if ($user->mail !== $request->input('mail')) {
            $this->userService->updateField($user, 'mail', $request->input('mail'));
        }

        if ($user->motto !== $request->input('motto')) {
            $this->rconService->setMotto($user, $request->input('motto'));
            $this->userService->updateField($user, 'motto', $request->input('motto'));
        }

        return redirect()->route('settings.account.show')->with('success', __('Your account settings has been updated'));
    }
    public function twoFactor(): View
    {
        return view('user.settings.two-factor');
    }
}
