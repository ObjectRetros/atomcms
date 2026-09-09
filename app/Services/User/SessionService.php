<?php

namespace App\Services\User;

use App\Data\SessionLogData;
use App\Data\SessionRecordData;
use App\Models\Session;
use App\Models\User;
use App\Support\AuthenticatedUser;
use Carbon\CarbonImmutable;
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
        return $this->forUser(AuthenticatedUser::from($request), $request->session()->getId())->map(fn (SessionRecordData $session): SessionLogData => new SessionLogData($session->agent, $session->ipAddress, $session->isCurrentDevice, $session->lastActive->diffForHumans()));
    }

    /** @return Collection<int, SessionRecordData> */
    public function forUser(User $user, string $currentSessionId): Collection
    {
        return $user->sessions->map(function ($session) use ($currentSessionId) {
            $agent = $this->createAgent($session);

            return new SessionRecordData(
                agent: [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                ipAddress: $session->ip_address,
                isCurrentDevice: $session->id === $currentSessionId,
                lastActive: CarbonImmutable::createFromTimestamp($session->last_activity, 'UTC'),
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
