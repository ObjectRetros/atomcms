<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketFormRequest;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Services\Help\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', WebsiteHelpCenterTicket::class);

        return view('help-center.tickets.index', [
            'tickets' => $this->ticketService->getPaginatedTickets(),
        ]);
    }

    public function create(): View
    {
        return view('help-center.tickets.create', [
            'categories' => $this->ticketService->getCategories(),
            'openTickets' => $this->ticketService->getOpenTicketsForUser(Auth::user()),
        ]);
    }

    public function store(WebsiteTicketFormRequest $request): RedirectResponse
    {
        $this->ticketService->createTicket(Auth::user(), $request->validated());

        return redirect()->back()->with('success', __('Ticket submitted!'));
    }

    public function show(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        return view('help-center.tickets.show', [
            'ticket' => $this->ticketService->loadTicketWithRelations($ticket),
            'openTickets' => $this->ticketService->getOpenTicketsForUser(Auth::user(), $ticket->id),
        ]);
    }

    public function edit(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('help-center.tickets.edit', [
            'ticket' => $this->ticketService->loadTicketWithRelations($ticket),
            'categories' => $this->ticketService->getCategories(),
            'openTickets' => $this->ticketService->getOpenTicketsForUser(Auth::user(), $ticket->id),
        ]);
    }

    public function update(WebsiteTicketFormRequest $request, WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $this->ticketService->updateTicket($ticket, $request->validated());

        return to_route('help-center.ticket.show', $ticket)->with('success', __('Ticket updated!'));
    }

    public function destroy(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $this->ticketService->deleteTicket($ticket);

        return to_route('me.show')->with('success', __('The ticket has been deleted!'));
    }

    public function toggleTicketStatus(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        $this->authorize('toggleStatus', $ticket);

        $this->ticketService->toggleStatus($ticket);

        return redirect()->back()->with('success', __('The ticket status has been changed!'));
    }
}
