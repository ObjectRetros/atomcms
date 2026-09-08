<?php

namespace App\Services\Help;

use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Models\Help\WebsiteRuleCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class TicketService
{
    /** @return Collection<int, WebsiteHelpCenterCategory> */
    public function categories(): Collection
    {
        return WebsiteHelpCenterCategory::orderBy('position')->get();
    }

    /** @return Collection<int, WebsiteRuleCategory> */
    public function rules(): Collection
    {
        return WebsiteRuleCategory::with('rules')->get();
    }

    /** @return LengthAwarePaginator<int, WebsiteHelpCenterTicket> */
    public function tickets(User $actor, bool $all = false): LengthAwarePaginator
    {
        if ($all) {
            Gate::forUser($actor)->authorize('viewAny', WebsiteHelpCenterTicket::class);
        }

        return WebsiteHelpCenterTicket::query()->when(! $all, fn ($query) => $query->where('user_id', $actor->id))->orderBy('open')->latest('id')->with('user:id,username,motto,look,online')->paginate(15);
    }

    public function show(User $actor, WebsiteHelpCenterTicket $ticket): WebsiteHelpCenterTicket
    {
        Gate::forUser($actor)->authorize('view', $ticket);

        return $ticket->load(['user:id,username,motto,look,online', 'category', 'replies.user:id,username,motto,look,online']);
    }

    /** @param array{category_id: int, title: string, content: string} $data */
    public function store(User $actor, array $data): WebsiteHelpCenterTicket
    {
        return $actor->tickets()->create($data);
    }

    /** @param array{category_id: int, title: string, content: string} $data */
    public function update(User $actor, WebsiteHelpCenterTicket $ticket, array $data): void
    {
        Gate::forUser($actor)->authorize('update', $ticket);
        $ticket->update($data);
    }

    public function destroy(User $actor, WebsiteHelpCenterTicket $ticket): void
    {
        Gate::forUser($actor)->authorize('delete', $ticket);
        $ticket->delete();
    }

    public function toggle(User $actor, WebsiteHelpCenterTicket $ticket): void
    {
        Gate::forUser($actor)->authorize('update', $ticket);
        $ticket->update(['open' => ! $ticket->open]);
    }

    public function reply(User $actor, WebsiteHelpCenterTicket $ticket, string $content): WebsiteHelpCenterTicketReply
    {
        Gate::forUser($actor)->authorize('reply', $ticket);

        return $ticket->replies()->create(['user_id' => $actor->id, 'content' => $content]);
    }

    public function destroyReply(User $actor, WebsiteHelpCenterTicketReply $reply): void
    {
        Gate::forUser($actor)->authorize('delete', $reply);
        $reply->delete();
    }
}
