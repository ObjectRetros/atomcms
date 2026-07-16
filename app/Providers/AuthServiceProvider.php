<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\Help\WebsiteHelpCenterTicketReply;
use App\Policies\ActivityPolicy;
use App\Policies\WebsiteHelpCenterTicketPolicy;
use App\Policies\WebsiteHelpCenterTicketReplyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Activity::class => ActivityPolicy::class,
        WebsiteHelpCenterTicket::class => WebsiteHelpCenterTicketPolicy::class,
        WebsiteHelpCenterTicketReply::class => WebsiteHelpCenterTicketReplyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
