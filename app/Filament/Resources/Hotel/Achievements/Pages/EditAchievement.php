<?php

namespace App\Filament\Resources\Hotel\Achievements\Pages;

use App\Filament\Resources\Hotel\Achievements\AchievementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAchievement extends EditRecord
{
    protected static string $resource = AchievementResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
