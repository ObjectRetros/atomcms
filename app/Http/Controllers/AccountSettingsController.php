<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountSettingsFormRequest;
use App\Rules\WebsiteWordfilterRule;
use App\Services\RconService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class AccountSettingsController extends Controller
{
    public function edit(): View
    {
        return view('user.settings.account', [
            'user' => Auth::user()->load('settings:allow_name_change'),
        ]);
    }

    public function sessionLogs(Request $request): View
    {
        $sessions = collect(
            Auth::user()->sessions
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });

        return view('user.settings.session-logs', [
            'logs' => $sessions,
        ]);
    }

    protected function createAgent($session): Agent
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function update(RconService $rcon, AccountSettingsFormRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $allowedNameChange = $user->settings?->allow_name_change && $user->username !== $request->input('username');

        if ($allowedNameChange) {
            // Disconnect the user and wait 1 second, to ensure the name change will proceed
            if ($rcon->isConnected && $user->online) {
                $rcon->disconnectUser($user);
                sleep(1);
            }

            $user->update([
                'username' => $request->input('username'),
            ]);
        }

        if ($user->mail !== $request->input('mail')) {
            $user->update([
                'mail' => $request->input('mail'),
            ]);
        }

        if ($user->motto !== $request->input('motto')) {
            if ($rcon->isConnected) {
                $rcon->setMotto($user, $request->input('motto'));
            } else {
                $user->update([
                    'motto' => $request->input('motto'),
                ]);
            }
        }

        return redirect()->back()->with('success', __('Your account settings has been updated'));
    }

    public function twoFactor(): View
    {
        return view('user.settings.two-factor');
    }
}
