<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Audit\UserAuditResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class AdminFilamentPanelProvider extends PanelProvider
{	
    public function panel(Panel $panel): Panel
	{
		FilamentView::registerRenderHook(
			'panels::body.end', fn () => view('components.footer')->render()
		);
	
        return $panel
            ->default()
            ->id('housekeeping')
            ->path('housekeeping')
            ->login()
            ->colors([
                'primary' => Color::Amber,
				'total-users' => Color::hex('#5edc2f'),
				'online-users' => Color::hex('#3333fe'),
				'total-furni' => Color::hex('#d1be00'),
				'total-rooms' => Color::hex('#df6311'),
				'total-photos' => Color::hex('#1da7a3'),
				'total-bans' => Color::hex('#ff0000'),
				'total-article' => Color::hex('#c141b2'),
				'total-article-visible' => Color::hex('#6c9f50'),
				'total-tickets' => Color::hex('#7f7f7f'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
			->plugins([
				ActivitylogPlugin::make()
				->resource(UserAuditResource::class)
					->label('Log')
					->pluralLabel('Audit Log')
					->navigationGroup('Audit')
					->navigationCountBadge(true)
                    ->authorize(function ($user) {
                        return $user->can('viewAny', ActivitylogResource::getModel());
                    }),
			]);
    }
}
