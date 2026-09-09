<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketFormRequest;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Services\Help\TicketService;
use App\Support\AuthenticatedUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', WebsiteHelpCenterTicket::class);

        return view('help-center.tickets.index', [
            'tickets' => app(TicketService::class)->tickets(AuthenticatedUser::current(), true),
        ]);
    }

    public function create(): View
    {
        return view('help-center.tickets.create', [
            'categories' => app(TicketService::class)->categories(),
            'openTickets' => $this->myOpenTickets(),
        ]);
    }

    public function store(WebsiteTicketFormRequest $request): RedirectResponse
    {
        app(TicketService::class)->store(AuthenticatedUser::from($request), $request->ticketData());

        return redirect()->back()->with('success', __('Ticket submitted!'));
    }

    public function edit(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('update', $ticket);

        $ticket = app(TicketService::class)->show(AuthenticatedUser::current(), $ticket);

        return view('help-center.tickets.edit', [
            'ticket' => $ticket,
            'categories' => app(TicketService::class)->categories(),
            'openTickets' => $this->myOpenTickets($ticket),
        ]);
    }

    public function update(WebsiteHelpCenterTicket $ticket, WebsiteTicketFormRequest $request): RedirectResponse
    {
        app(TicketService::class)->update(AuthenticatedUser::from($request), $ticket, $request->ticketData());

        return to_route('help-center.ticket.show', $ticket)->with('success', __('Ticket updated!'));
    }

    public function show(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket = app(TicketService::class)->show(AuthenticatedUser::current(), $ticket);

        return view('help-center.tickets.show', [
            'ticket' => $ticket,
            'openTickets' => $this->myOpenTickets($ticket),
        ]);
    }

    public function destroy(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        app(TicketService::class)->destroy(AuthenticatedUser::current(), $ticket);

        return to_route('me.show')->with('success', __('The ticket has been deleted!'));
    }

    public function toggleTicketStatus(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        app(TicketService::class)->toggle(AuthenticatedUser::current(), $ticket);

        return redirect()->back()->with('success', __('The ticket status has been changed!'));
    }

    /**
     * The current user's open tickets, optionally excluding the one being
     * viewed or edited.
     *
     * @return Collection<int, WebsiteHelpCenterTicket>
     */
    private function myOpenTickets(?WebsiteHelpCenterTicket $except = null): Collection
    {
        $query = WebsiteHelpCenterTicket::where('open', true)
            ->where('user_id', Auth::id());

        if ($except !== null) {
            $query->whereKeyNot($except->getKey());
        }

        return $query->get();
    }
}
