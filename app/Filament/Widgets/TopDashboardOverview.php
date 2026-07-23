<?php

namespace App\Filament\Widgets;

use App\Models\ItemDefinition;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\Room;
use App\Models\User;
use App\Models\WebsiteBadge;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class TopDashboardOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $counts = Cache::remember('housekeeping.dashboard.counts', 300, fn (): array => [
            'users' => User::count(),
            'furniture' => ItemDefinition::count(),
            'rooms' => Room::count(),
            'photos' => CameraWeb::count(),
            'badges' => WebsiteBadge::count(),
        ]);

        return [
            Stat::make(__('filament::resources.stats.users_count.title'), Number::format($counts['users'], 0, 1, app()->getLocale()))
                ->description(__('filament::resources.stats.users_count.description'))
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.furniture_count.title'), Number::format($counts['furniture'], 0, 1, app()->getLocale()))
                ->description(__('filament::resources.stats.furniture_count.description'))
                ->descriptionIcon('heroicon-m-cube', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.rooms_count.title'), Number::format($counts['rooms'], 0, 1, app()->getLocale()))
                ->description(__('filament::resources.stats.rooms_count.description'))
                ->descriptionIcon('heroicon-m-building-storefront', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.photos_count.title'), Number::format($counts['photos'], 0, 1, app()->getLocale()))
                ->description(__('filament::resources.stats.photos_count.description'))
                ->descriptionIcon('heroicon-m-camera', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.badge_count.title'), Number::format($counts['badges'], 0, 1, app()->getLocale()))
                ->description(__('filament::resources.stats.badge_count.description'))
                ->descriptionIcon('heroicon-m-gif', IconPosition::Before)
                ->color('success'),
        ];
    }
}
