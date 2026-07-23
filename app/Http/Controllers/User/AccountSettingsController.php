<?php

namespace App\Http\Controllers\User;

use App\Actions\User\UpdateAccountSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Services\User\SessionService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(): View
    {
        return view('user.settings.account', [
            'user' => AuthenticatedUser::current()->load('settings:id,user_id,allow_name_change'),
        ]);
    }

    public function sessionLogs(Request $request, SessionService $sessionService): View
    {
        $sessions = $sessionService->fetchSessionLogs($request);

        return view('user.settings.session-logs', [
            'logs' => $sessions,
        ]);
    }

    public function update(AccountSettingsFormRequest $request, UpdateAccountSettings $updateAccountSettings): RedirectResponse
    {
        $updateAccountSettings->execute(AuthenticatedUser::from($request), $request->validated());

        return redirect()->route('settings.account.show')->with('success', __('Your account settings has been updated'));
    }
}
