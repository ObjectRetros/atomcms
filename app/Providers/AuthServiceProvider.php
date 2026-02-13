<?php

namespace App\Providers;

use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User\WebsiteUserGuestbook;
use App\Policies\ActivityPolicy;
use App\Policies\GuestbookPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WebsiteHelpCenterTicket::class => TicketPolicy::class,
        WebsiteUserGuestbook::class => GuestbookPolicy::class,
        Activity::class => ActivityPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
