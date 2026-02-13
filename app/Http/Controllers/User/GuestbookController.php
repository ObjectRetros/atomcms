<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuestbookFormRequest;
use App\Models\User;
use App\Models\User\WebsiteUserGuestbook;
use App\Services\User\GuestbookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class GuestbookController extends Controller
{
    public function __construct(
        private readonly GuestbookService $guestbookService,
    ) {}

    public function store(User $user, GuestbookFormRequest $request): RedirectResponse
    {
        $this->authorize('create', [WebsiteUserGuestbook::class, $user]);

        $this->guestbookService->postMessage(
            $user,
            Auth::user(),
            $request->input('message'),
        );

        return redirect()->back()->with('success', __('Your message has been posted.'));
    }

    public function destroy(User $user, WebsiteUserGuestbook $guestbook): RedirectResponse
    {
        $this->authorize('delete', [$guestbook, $user]);

        $this->guestbookService->deleteMessage($guestbook);

        return redirect()->back()->with('success', __('Your message has been deleted.'));
    }
}
