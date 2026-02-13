<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\User\WebsiteUserGuestbook;

class GuestbookService
{
    public function postMessage(User $profileOwner, User $author, string $message): WebsiteUserGuestbook
    {
        return $profileOwner->profileGuestbook()->create([
            'user_id' => $author->id,
            'message' => $message,
        ]);
    }

    public function deleteMessage(WebsiteUserGuestbook $guestbook): bool
    {
        return $guestbook->delete();
    }
}
