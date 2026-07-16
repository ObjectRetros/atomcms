<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Articles\WebsiteArticleComment;
use App\Policies\ActivityPolicy;
use App\Policies\WebsiteArticleCommentPolicy;
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
        Activity::class => ActivityPolicy::class,
        WebsiteArticleComment::class => WebsiteArticleCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
