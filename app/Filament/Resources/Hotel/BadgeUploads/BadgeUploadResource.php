<?php

namespace App\Filament\Resources\Hotel\BadgeUploads;

use App\Filament\Resources\Hotel\BadgeUploads\Pages\ManageBadgeUploads;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BadgeUploadResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gif';

    protected static ?string $label = 'Badge Upload';

    public static function canViewAny(): bool
    {
        return hasHousekeepingPermission('manage_badges');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')
                    ->label('File Name')
                    ->sortable(),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBadgeUploads::route('/'),
        ];
    }

    /** @return array<int, array{filename: string, path: string}> */
    public static function getFiles(): array
    {
        $files = Storage::disk('badges')->files();

        return collect($files)
            ->filter(fn (string $file): bool => strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'gif')
            ->map(fn (string $file): array => ['filename' => basename($file)])
            ->values()
            ->toArray();
    }
}
