<?php

namespace App\Policies;

use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;
use App\Services\PermissionsService;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsiteHelpCenterTicketPolicy
{
    use HandlesAuthorization;

    public function __construct(private readonly PermissionsService $permissions) {}

    /**
     * The ticket overview lists every user's tickets, so it is staff-only.
     */
    public function viewAny(User $user): bool
    {
        return $this->permissions->allows($user, 'manage_website_tickets');
    }

    public function view(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $this->update($user, $ticket);
    }

    public function update(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id
            || $this->permissions->allows($user, 'manage_website_tickets');
    }

    public function delete(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id
            || $this->permissions->allows($user, 'delete_website_tickets');
    }

    public function reply(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return (bool) $ticket->open && $this->update($user, $ticket);
    }
}
