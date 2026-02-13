<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Services\RconService;
use App\Services\User\SessionService;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function __construct(
        private readonly SessionService $sessionService,
        private readonly UserService $userService,
        private readonly RconService $rconService,
    ) {}

    public function edit(): View
    {
        return view('user.settings.account', [
            'user' => Auth::user()->load('settings:allow_name_change'),
        ]);
    }

    public function update(AccountSettingsFormRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $this->rconService->isConnected() && $user->online === '1') {
            return back()->withErrors('You must be offline to change your account settings');
        }

        $this->updateUserFields($user, $request);

        return redirect()
            ->route('settings.account.show')
            ->with('success', __('Your account settings has been updated'));
    }

    public function sessionLogs(Request $request): View
    {
        return view('user.settings.session-logs', [
            'logs' => $this->sessionService->fetchSessionLogs($request),
        ]);
    }

    private function updateUserFields($user, AccountSettingsFormRequest $request): void
    {
        if ($user->mail !== $request->input('mail')) {
            $this->userService->updateField($user, 'mail', $request->input('mail'));
        }

        if ($user->motto !== $request->input('motto')) {
            $this->rconService->setMotto($user, $request->input('motto'));
            $this->userService->updateField($user, 'motto', $request->input('motto'));
        }
    }
}
