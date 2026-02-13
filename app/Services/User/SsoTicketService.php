<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Str;

class SsoTicketService
{
    public function generateFor(User $user): string
    {
        $sso = $this->generateTicket();

        if (User::where('auth_ticket', $sso)->exists()) {
            return $this->generateFor($user);
        }

        $user->update(['auth_ticket' => $sso]);

        return $sso;
    }

    private function generateTicket(): string
    {
        return sprintf(
            '%s-%s',
            Str::replace(' ', '', setting('hotel_name')),
            Str::uuid(),
        );
    }
}
