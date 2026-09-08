<?php

namespace App\Services\Client;

use App\Models\User;

class ClientLaunchService
{
    public function ticket(User $user, string $ipAddress): string
    {
        $user->update(['ip_current' => $ipAddress]);

        return $user->ssoTicket();
    }
}
