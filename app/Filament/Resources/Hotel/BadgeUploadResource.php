<?php

namespace App\Filament\Resources\Hotel;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\Hotel\BadgeUploadResource\Pages;

class BadgeUploadResource extends Resource
{
    protected static ?string $navigationGroup = 'Hotel';
	protected static ?string $navigationIcon = 'heroicon-o-gif';
    protected static ?string $label = 'Badge Upload';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('badge_file')
                    ->label('Upload Badge')
                    ->disk('local')
                    ->directory(env('BadgePath', 'badges'))
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('filename')
                    ->label('File Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('path')
                    ->label('File Path'),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBadgeUploads::route('/'),
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
