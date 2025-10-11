<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuestbookFormRequest;
use App\Models\User;
use App\Models\User\WebsiteUserGuestbook;
use Illuminate\Support\Facades\Auth;

class GuestbookController extends Controller
{
    public function store(User $user, GuestbookFormRequest $request)
    {
        $this->validateGuestbookPost($user, $request);

        $user->profileGuestbook()->create([
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
        ]);

        return redirect()->back()->with('success', __('Your message has been posted.'));
    }

    public function destroy(User $user, WebsiteUserGuestbook $guestbook)
    {
        if ($guestbook->user_id !== Auth::id() && $guestbook->profile_id !== $user->id && Auth::user()->rank < (int) setting('min_staff_rank')) {
            return redirect()->back()->withErrors([
                'message' => __('Do do not have permission to delete this message'),
            ]);
        }

        $guestbook->delete();

        return redirect()->back()->with('success', __('Your message has been deleted.'));
    }

    private function validateGuestbookPost(User $user, GuestbookFormRequest $request)
    {
        if ($user->id === $request->user()->id) {
            return $this->redirectWithError(__('You cannot post a message on your own profile.'));
        }

        $maxAllowedPostCount = ! empty(setting('max_guestbook_posts_per_profile')) ? (int) setting('max_guestbook_posts_per_profile') : 3;
        if ($user->profileGuestbook()->where('user_id', $request->user()->id)->count() >= $maxAllowedPostCount) {
            return $this->redirectWithError(__('You have already posted :count messages on this profile.', ['count' => $maxAllowedPostCount]));
        }
    }

    private function redirectWithError($message)
    {
        return redirect()->back()->withErrors(['message' => $message]);
    }
}
