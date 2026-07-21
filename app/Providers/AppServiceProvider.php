<?php

namespace App\Providers;

use App\Contracts\PaypalGateway;
use App\Contracts\Rcon;
use App\Models\WebsiteDrawBadge;
use App\Observers\WebsiteDrawBadgeObserver;
use App\Services\AfterCommitRcon;
use App\Services\HousekeepingPermissionsService;
use App\Services\InstallationService;
use App\Services\Payments\SrmklivePaypalGateway;
use App\Services\PermissionsService;
use App\Services\RconService;
use App\Services\SettingsService;
use App\Services\ViteService;
use Filament\Tables\Table;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Blaze\Blaze;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaypalGateway::class, SrmklivePaypalGateway::class);

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

        $this->app->singleton(
            HousekeepingPermissionsService::class,
            fn () => new HousekeepingPermissionsService,
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
            $client = new PayPalClient(config('habbo.paypal'));
            $client->setClient(new HttpClient([
                'connect_timeout' => 3,
                'timeout' => 10,
                'verify' => true,
            ]));
            $client->getAccessToken();

            return $client;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn (): Password => Password::min(12)
            ->mixedCase()
            ->numbers()
            ->symbols());

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

        WebsiteDrawBadge::observe(WebsiteDrawBadgeObserver::class);
    }
}
