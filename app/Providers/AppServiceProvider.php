<?php

namespace App\Providers;

use App\Contracts\Rcon;
use App\Models\WebsiteDrawBadge;
use App\Observers\WebsiteDrawBadgeObserver;
use App\Services\AfterCommitRcon;
use App\Services\InstallationService;
use App\Services\PermissionsService;
use App\Services\RconService;
use App\Services\SettingsService;
use App\Services\ViteService;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Blaze\Blaze;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            Vite::class,
            ViteService::class,
        );

        $this->app->singleton(
            InstallationService::class,
            fn () => new InstallationService,
        );

        $this->app->singleton(
            SettingsService::class,
            fn () => new SettingsService,
        );

        $this->app->singleton(
            PermissionsService::class,
            fn () => new PermissionsService,
        );

        // Wrapped so RCON sends inside a DB transaction only fire once it
        // commits - a rolled-back purchase never grants items in the emulator.
        $this->app->singleton(
            Rcon::class,
            fn () => new AfterCommitRcon(new RconService),
        );

        // Resolve the PayPal client pre-authenticated so consumers can inject
        // it and tests can swap it for a fake.
        $this->app->bind(PayPalClient::class, function (): PayPalClient {
            $client = new PayPalClient;
            $client->setApiCredentials(config('habbo.paypal'));
            $client->getAccessToken();

            return $client;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Blaze::optimize()
            ->in(resource_path('themes/atom/components'))
            ->in(resource_path('themes/dusk/components'));

        if (config('habbo.site.force_https')) {
            URL::forceScheme('https');
        }

        Table::configureUsing(function (Table $table) {
            $table->paginated([10, 25, 50]);
        });

        $settingsService = app(SettingsService::class);
        $badgePath = $settingsService->getOrDefault('badge_path_filesystem', '/var/www/gamedata/c_images/album1584');
        Config::set('filesystems.disks.badges.root', $badgePath);

        $adsPath = $settingsService->getOrDefault('ads_path_filesystem', '/var/www/gamedata/custom');
        Config::set('filesystems.disks.ads.root', $adsPath);

        WebsiteDrawBadge::observe(WebsiteDrawBadgeObserver::class);
    }
}
