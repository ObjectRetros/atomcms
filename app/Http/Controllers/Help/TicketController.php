<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketFormRequest;
use App\Models\Help\WebsiteHelpCenterCategory;
use App\Models\Help\WebsiteHelpCenterTicket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        if (! hasPermission('manage_website_tickets')) {
            return redirect()->back()->with([
                'message' => __('You cannot access this page'),
            ]);
        }

        return view('help-center.tickets.index', [
            'tickets' => WebsiteHelpCenterTicket::orderBy('open')->with('user:id,username')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('help-center.tickets.create', [
            'categories' => WebsiteHelpCenterCategory::get(),
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->where('user_id', Auth::id())->get(),
        ]);
    }

    public function store(WebsiteTicketFormRequest $request)
    {
        Auth::user()->tickets()->create($request->validated());

        return redirect()->back()->with('success', __('Ticket submitted!'));
    }

    public function edit(WebsiteHelpCenterTicket $ticket)
    {
        if (! $ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot manage others tickets.'),
            ]);
        }

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

    public function update(WebsiteHelpCenterTicket $ticket, WebsiteTicketFormRequest $request)
    {
        if (! $ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot manage others tickets.'),
            ]);
        }

        $ticket->update($request->validated());

        return to_route('help-center.ticket.show', $ticket)->with('success', __('Ticket updated!'));
    }

    public function show(WebsiteHelpCenterTicket $ticket)
    {
        if (! $ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot view others tickets.'),
            ]);
        }

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

    public function destroy(WebsiteHelpCenterTicket $ticket)
    {
        if (! $ticket->canDeleteTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot delete others tickets.'),
            ]);
        }

        $ticket->delete();

        return to_route('me.show')->with('success', __('The ticket has been deleted!'));
    }

    public function toggleTicketStatus(WebsiteHelpCenterTicket $ticket)
    {
        if (! $ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You manage others tickets.'),
            ]);
        }

        $ticket->open ? $ticket->update(['open' => false]) : $ticket->update(['open' => true]);

        return redirect()->back()->with('success', __('The ticket status has been changed!'));
    }
}
