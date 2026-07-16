<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketReplyFormRequest;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use Illuminate\Http\RedirectResponse;

class TicketReplyController extends Controller
{
    public function store(WebsiteHelpCenterTicket $ticket, WebsiteTicketReplyFormRequest $request): RedirectResponse
    {
        $this->authorize('reply', $ticket);

        $data = $request->validated();
        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        return redirect()->back()->with('success', __('The reply has been submitted!'));
    }

    public function destroy(WebsiteHelpCenterTicketReply $reply): RedirectResponse
    {
        $this->authorize('delete', $reply);

        $reply->delete();

        return redirect()->back()->with('success', __('The reply has been deleted!'));
    }
}
