<?php

namespace App\Services\User;

use App\Data\SessionLogData;
use App\Models\Session;
use App\Support\AuthenticatedUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;

class SessionService
{
    /**
     * @return Collection<int, SessionLogData>
     */
    public function fetchSessionLogs(Request $request): Collection
    {
        return collect(
            AuthenticatedUser::from($request)->sessions,
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return new SessionLogData(
                agent: [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                ipAddress: $session->ip_address,
                isCurrentDevice: $session->id === $request->session()->getId(),
                lastActive: Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            );
        });
    }

    protected function createAgent(Session $session): Agent
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
