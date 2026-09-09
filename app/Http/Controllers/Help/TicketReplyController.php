<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteTicketReplyFormRequest;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Services\Help\TicketService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;

class TicketReplyController extends Controller
{
    public function store(WebsiteHelpCenterTicket $ticket, WebsiteTicketReplyFormRequest $request): RedirectResponse
    {
        app(TicketService::class)->reply(AuthenticatedUser::from($request), $ticket, $request->validated('content'));

        return redirect()->back()->with('success', __('The reply has been submitted!'));
    }

    public function destroy(WebsiteHelpCenterTicketReply $reply): RedirectResponse
    {
        app(TicketService::class)->destroyReply(AuthenticatedUser::current(), $reply);

        return redirect()->back()->with('success', __('The reply has been deleted!'));
    }
}
