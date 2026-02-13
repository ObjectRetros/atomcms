<?php

namespace App\Policies;

use App\Models\User;
use App\Models\User\WebsiteUserGuestbook;

class GuestbookPolicy
{
    public function create(User $user, User $profileOwner): bool
    {
        if ($user->id === $profileOwner->id) {
            return false;
        }

        $maxPosts = (int) setting('max_guestbook_posts_per_profile') ?: 3;
        $currentCount = $profileOwner->profileGuestbook()
            ->where('user_id', $user->id)
            ->count();

        return $currentCount < $maxPosts;
    }

    public function delete(User $user, WebsiteUserGuestbook $guestbook, User $profileOwner): bool
    {
        if ($guestbook->user_id === $user->id) {
            return true;
        }

        if ($guestbook->profile_id === $profileOwner->id && $profileOwner->id === $user->id) {
            return true;
        }

        return $user->rank >= (int) setting('min_staff_rank');
    }
}
