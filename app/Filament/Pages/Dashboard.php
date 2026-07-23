<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatableResource;
use Filament\Pages\Dashboard as FilamentDashboard;

class Dashboard extends FilamentDashboard
{
    use TranslatableResource;

    protected static string|\UnitEnum|null $navigationGroup = 'Dashboard';

    protected static ?string $navigationLabel = 'Homepage';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public static string $translateIdentifier = 'dashboard';

    public static function canAccess(): bool
    {
        // Panel access is already gated by User::canAccessPanel(); every
        // authenticated housekeeping user may see the dashboard.
        return auth()->check();
    }
}
