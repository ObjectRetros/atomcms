<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketReplyFormRequest;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;

class TicketReplyController extends Controller
{
    public function store(WebsiteHelpCenterTicket $ticket, WebsiteTicketReplyFormRequest $request): RedirectResponse
    {
        if (! $ticket->isOpen()) {
            return redirect()->back()->with([
                'message' => __('You cannot reply to the ticket as it has been closed.'),
            ]);
        }

        if (! $ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot reply to others tickets.'),
            ]);
        }

        $data = $request->validated();
        $ticket->replies()->create([
            'user_id' => AuthenticatedUser::from($request)->id,
            'content' => $data['content'],
        ]);

        return redirect()->back()->with('success', __('The reply has been submitted!'));
    }

    public function destroy(WebsiteHelpCenterTicketReply $reply): RedirectResponse
    {
        if (! $reply->canDeleteReply()) {
            return redirect()->back()->with([
                'message' => __('You do not have permission to delete this reply.'),
            ]);
        }

        $reply->delete();

        return redirect()->back()->with('success', __('The reply has been deleted!'));
    }
}
