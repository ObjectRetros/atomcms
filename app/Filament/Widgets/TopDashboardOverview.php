<?php

namespace App\Filament\Widgets;

use App\Emulator\Contracts\FurnitureRepository;
use App\Emulator\Contracts\RoomRepository;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\User;
use App\Models\WebsiteBadge;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class TopDashboardOverview extends BaseWidget
{
    private const CACHE_KEY = 'housekeeping.dashboard.counts';

    private const CACHE_SECONDS = 300;

    protected static ?int $sort = 1;

    /** Counting whole emulator tables is far too costly to repeat on a poll. */
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $counts = $this->counts();

        return [
            Stat::make(__('filament::resources.stats.users_count.title'), $this->format($counts['users']))
                ->description(__('filament::resources.stats.users_count.description'))
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.furniture_count.title'), $this->format($counts['furniture']))
                ->description(__('filament::resources.stats.furniture_count.description'))
                ->descriptionIcon('heroicon-m-cube', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.rooms_count.title'), $this->format($counts['rooms']))
                ->description(__('filament::resources.stats.rooms_count.description'))
                ->descriptionIcon('heroicon-m-building-storefront', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.photos_count.title'), $this->format($counts['photos']))
                ->description(__('filament::resources.stats.photos_count.description'))
                ->descriptionIcon('heroicon-m-camera', IconPosition::Before)
                ->color('success'),

            Stat::make(__('filament::resources.stats.badge_count.title'), $this->format($counts['badges']))
                ->description(__('filament::resources.stats.badge_count.description'))
                ->descriptionIcon('heroicon-m-gif', IconPosition::Before)
                ->color('success'),
        ];
    }

    /**
     * Furniture and rooms live in tables whose shape only the active driver
     * knows, so both are counted through its repository rather than through an
     * Arcturus model. Photos read zero on a driver that stores none.
     *
     * @return array<string, int>
     */
    private function counts(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, fn (): array => [
            'users' => User::count(),
            'furniture' => app(FurnitureRepository::class)->definitionCount(),
            'rooms' => app(RoomRepository::class)->count(),
            'photos' => Emulator::supports(Feature::CameraPhotos) ? CameraWeb::count() : 0,
            'badges' => WebsiteBadge::count(),
        ]);
    }

    private function format(int $count): string
    {
        return (string) Number::format($count, 0, 1, app()->getLocale());
    }
}
