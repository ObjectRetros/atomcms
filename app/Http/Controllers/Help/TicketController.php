<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketFormRequest;
use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', WebsiteHelpCenterTicket::class);

        return view('help-center.tickets.index', [
            'tickets' => WebsiteHelpCenterTicket::orderBy('open')->with('user:id,username')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('help-center.tickets.create', [
            'categories' => WebsiteHelpCenterCategory::get(),
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->where('user_id', Auth::id())->get(),
        ]);
    }

    public function store(WebsiteTicketFormRequest $request): RedirectResponse
    {
        Auth::user()->tickets()->create($request->validated());

        return redirect()->back()->with('success', __('Ticket submitted!'));
    }

    public function edit(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('update', $ticket);

        $ticket->load([
            'user:id,username,look',
            'category',
            'replies.user:id,username,look',
        ]);

        return view('help-center.tickets.edit', [
            'ticket' => $ticket,
            'categories' => WebsiteHelpCenterCategory::get(),
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->where('id', '!=', $ticket->id)->where('user_id', Auth::id())->get(),
        ]);
    }

    public function update(WebsiteHelpCenterTicket $ticket, WebsiteTicketFormRequest $request): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());

        return to_route('help-center.ticket.show', $ticket)->with('success', __('Ticket updated!'));
    }

    public function show(WebsiteHelpCenterTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'user:id,username,look',
            'category',
            'replies.user:id,username,look',
        ]);

        return view('help-center.tickets.show', [
            'ticket' => $ticket,
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->where('id', '!=', $ticket->id)->where('user_id', Auth::id())->get(),
        ]);
    }

    public function destroy(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return to_route('me.show')->with('success', __('The ticket has been deleted!'));
    }

    public function toggleTicketStatus(WebsiteHelpCenterTicket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update(['open' => ! $ticket->open]);

        return redirect()->back()->with('success', __('The ticket status has been changed!'));
    }
}
