<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Camera;
use App\Models\ItemDefinition;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\Room;
use App\Models\Help\WebsiteHelpCenterTicket;
use App\Models\User\Ban;
use App\Models\User;
use Illuminate\Support\Number;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TopDashboardOverview extends BaseWidget
{
	protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('filament::resources.stats.users_count.title'), Number::format(User::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.users_count.description'))
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('total-users'),
			
			Stat::make(__('filament::resources.stats.online_users_count.title'), Number::format(User::where('online', '1')->count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.online_users_count.description'))
                ->descriptionIcon('heroicon-m-users', IconPosition::Before)
                ->color('online-users'),

            Stat::make(__('filament::resources.stats.furniture_count.title'), Number::format(ItemDefinition::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.furniture_count.description'))
                ->descriptionIcon('heroicon-o-cube', IconPosition::Before)
                ->color('total-furni'),

            Stat::make(__('filament::resources.stats.rooms_count.title'), Number::format(Room::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.rooms_count.description'))
                ->descriptionIcon('heroicon-o-building-storefront', IconPosition::Before)
                ->color('total-rooms'),
				
            Stat::make(__('filament::resources.stats.photos_count.title'), Number::format(CameraWeb::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.photos_count.description'))
                ->descriptionIcon('heroicon-o-camera', IconPosition::Before)
                ->color('total-photos'),
				
			Stat::make(__('filament::resources.stats.bans_count.title'), Number::format(Ban::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.bans_count.description'))
                ->descriptionIcon('heroicon-m-no-symbol', IconPosition::Before)
                ->color('total-bans'),
			
			Stat::make(__('filament::resources.stats.article_count.title'), Number::format(Article::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.article_count.description'))
                ->descriptionIcon('heroicon-m-folder', IconPosition::Before)
                ->color('total-article'),
			
			Stat::make(__('filament::resources.stats.visible_article_count.title'), Number::format(Article::whereNull('deleted_at')->count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.visible_article_count.description'))
                ->descriptionIcon('heroicon-o-book-open', IconPosition::Before)
                ->color('total-article-visible'),
				
			Stat::make(__('filament::resources.stats.help_center_tickets_count.title'), Number::format(WebsiteHelpCenterTicket::count(), '0', '1', app()->getLocale()))
                ->description(__('filament::resources.stats.help_center_tickets_count.description'))
                ->descriptionIcon('heroicon-o-ticket', IconPosition::Before)
                ->color('total-tickets'),
        ];
    }
}
