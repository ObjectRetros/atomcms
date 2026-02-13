<?php

namespace App\Policies;

use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return hasPermission('manage_website_tickets');
    }

    public function view(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('manage_website_tickets');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('manage_website_tickets');
    }

    public function delete(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('delete_website_tickets');
    }

    public function toggleStatus(User $user, WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->user_id === $user->id || hasPermission('manage_website_tickets');
    }
}
