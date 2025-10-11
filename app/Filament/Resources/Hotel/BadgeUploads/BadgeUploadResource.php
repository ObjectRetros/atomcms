<?php

namespace App\Filament\Resources\Hotel\BadgeUploads;

use App\Filament\Resources\Hotel\BadgeUploads\Pages\ManageBadgeUploads;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BadgeUploadResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gif';

    protected static ?string $label = 'Badge Upload';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('badge_file')
                    ->label('Upload Badge')
                    ->disk('local')
                    ->directory(setting('badge_path_filesystem'))
                    ->required()
                    ->getUploadedFileNameForStorageUsing(
                        function (TemporaryUploadedFile $file): string {
                            return strtolower(str_replace([' ', '-', 'æ', 'ø', 'å'], ['_', '_', 'ae', 'oe', 'aa'], $file->getClientOriginalName()));
                        }
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')
                    ->label('File Name')
                    ->sortable(),
                TextColumn::make('path')
                    ->label('File Path'),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBadgeUploads::route('/'),
        ];
    }

    public static function getFiles(): array
    {
        $badgePath = env('BadgePath', 'badges');
        $files = Storage::disk('local')->files($badgePath);

        return collect($files)->map(function ($file) {
            return [
                'filename' => basename($file),
                'path' => $file,
            ];
        })->toArray();
    }
}
