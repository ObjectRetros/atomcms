<?php

namespace App\Services\Client;

use App\Models\User;
use App\Services\User\SsoTicketService;
use Illuminate\Http\Request;

class ClientService
{
    public function __construct(
        private readonly SsoTicketService $ssoTicketService,
    ) {}

    public function prepareClientSession(User $user, Request $request): string
    {
        $user->update(['ip_current' => $request->ip()]);

        return $this->ssoTicketService->generateFor($user);
    }
}
