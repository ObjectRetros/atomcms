<?php

namespace App\Policies;

use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Models\User;
use App\Services\PermissionsService;

class WebsiteHelpCenterTicketReplyPolicy
{
    public function __construct(private readonly PermissionsService $permissions) {}

    public function delete(User $user, WebsiteHelpCenterTicketReply $reply): bool
    {
        return $reply->user_id === $user->id
            || $this->permissions->allows($user, 'delete_website_ticket_replies');
    }
}
