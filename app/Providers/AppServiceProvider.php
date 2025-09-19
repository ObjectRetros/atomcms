<?php

namespace App\Providers;

use App\Exceptions\MigrationFailedException;
use App\Observers\WebsiteDrawBadgeObserver;
use App\Services\PermissionsService;
use App\Services\RconService;
use App\Services\SettingsService;
use App\Models\WebsiteDrawBadge;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Illuminate\Foundation\Vite::class,
            \App\Services\ViteService::class
        );

        $this->app->singleton(
            SettingsService::class,
            fn () => new SettingsService()
        );

        $this->app->singleton(
            PermissionsService::class,
            fn () => new PermissionsService()
        );

        $this->app->singleton(
            RconService::class,
            fn () => new RconService()
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
