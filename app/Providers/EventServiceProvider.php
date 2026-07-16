<?php

namespace App\Providers;

use App\Models\Community\Staff\WebsiteTeam as StaffWebsiteTeam;
use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use App\Observers\CommunityCacheObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    protected $observers = [
        User::class => [UserObserver::class, CommunityCacheObserver::class],
        Permission::class => [CommunityCacheObserver::class],
        StaffWebsiteTeam::class => [CommunityCacheObserver::class],
        WebsiteTeam::class => [CommunityCacheObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {}

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
