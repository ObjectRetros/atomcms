<?php

namespace App\Filament\Resources\Hotel\BadgeUploads;

use App\Filament\Resources\Hotel\BadgeUploads\Pages\ManageBadgeUploads;
use Filament\Resources\Resource;

class BadgeUploadResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gif';

    protected static ?string $label = 'Badge Upload';

    public static function canViewAny(): bool
    {
        return hasHousekeepingPermission('manage_badges');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBadgeUploads::route('/'),
        ];
    }
}
