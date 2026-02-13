<?php

namespace App\Services\Help;

use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function getPaginatedTickets(int $perPage = 15): LengthAwarePaginator
    {
        return WebsiteHelpCenterTicket::query()
            ->orderBy('open')
            ->with('user:id,username')
            ->paginate($perPage);
    }

    public function getCategories(): Collection
    {
        return WebsiteHelpCenterCategory::all();
    }

    public function getOpenTicketsForUser(User $user, ?int $excludeId = null): Collection
    {
        return WebsiteHelpCenterTicket::query()
            ->where('open', true)
            ->where('user_id', $user->id)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get();
    }

    public function createTicket(User $user, array $data): WebsiteHelpCenterTicket
    {
        return $user->tickets()->create($data);
    }

    public function updateTicket(WebsiteHelpCenterTicket $ticket, array $data): bool
    {
        return $ticket->update($data);
    }

    public function deleteTicket(WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->delete();
    }

    public function toggleStatus(WebsiteHelpCenterTicket $ticket): bool
    {
        return $ticket->update(['open' => ! $ticket->open]);
    }

    public function loadTicketWithRelations(WebsiteHelpCenterTicket $ticket): WebsiteHelpCenterTicket
    {
        return $ticket->load([
            'user:id,username,look',
            'category',
            'replies.user:id,username,look',
        ]);
    }
}
