<?php

namespace App\Policies;

use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsiteHelpCenterTicketPolicy
{
    use HandlesAuthorization;

    /**
     * The ticket overview lists every user's tickets, so it is staff-only.
     */
    public function viewAny(User $user): bool
    {
        return hasPermission('manage_website_tickets');
    }

    public function view(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $this->update($user, $ticket);
    }

    public function update(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('manage_website_tickets');
    }

    public function delete(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('delete_website_tickets');
    }
}
