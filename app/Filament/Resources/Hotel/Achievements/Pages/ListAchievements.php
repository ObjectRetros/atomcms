<?php

namespace App\Filament\Resources\Hotel\Achievements\Pages;

use App\Filament\Resources\Hotel\Achievements\AchievementResource;
use Filament\Resources\Pages\ListRecords;

class ListAchievements extends ListRecords
{
    protected static string $resource = AchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
