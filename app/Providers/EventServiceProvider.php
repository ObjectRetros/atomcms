<?php

namespace App\Providers;

use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Teams\WebsiteTeam;
use App\Models\Game\Permission;
use App\Models\User;
use App\Models\WebsiteAd;
use App\Observers\CommunityCacheObserver;
use App\Observers\UserObserver;
use App\Observers\WebsiteAdObserver;
use App\Observers\WebsiteOpenPositionObserver;
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
        WebsiteTeam::class => [CommunityCacheObserver::class],
        WebsiteAd::class => [WebsiteAdObserver::class],
        WebsiteOpenPosition::class => [WebsiteOpenPositionObserver::class],
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
