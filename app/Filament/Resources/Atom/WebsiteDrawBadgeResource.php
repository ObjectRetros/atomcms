<?php

namespace App\Filament\Resources\Atom;

use App\Models\WebsiteDrawBadge;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Atom\WebsiteDrawBadgeResource\Pages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WebsiteDrawBadgeResource extends Resource
{
    protected static ?string $model = WebsiteDrawBadge::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $slug = 'draw-badges';

    protected static ?string $pluralModelLabel = 'draw badges';

    protected static ?string $navigationLabel = 'Draw Badges';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('badge_name')
                    ->label(__('Badge Name'))
                    ->nullable()
                    ->maxLength(24)
                    ->autocomplete(false),
                TextInput::make('badge_desc')
                    ->label(__('Badge Description'))
                    ->nullable()
                    ->maxLength(255)
                    ->autocomplete(false)
                    ->columnSpanFull(),
                Toggle::make('published')
                    ->label(__('Published'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label(__('User ID')),
				TextColumn::make('user.username')
                    ->label(__('Username'))
                    ->sortable()
                    ->searchable(),
				TextColumn::make('badge_name')
					->limit(8)
                    ->label(__('Badge Name')),
				TextColumn::make('badge_desc')
                    ->label(__('Badge description'))
                    ->limit(35)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),
                ImageColumn::make('badge_url')
                    ->label(__('Badge'))
                    ->getStateUsing(fn ($record) => config('app.url') . $record->badge_url)
                    ->extraAttributes(['style' => 'image-rendering: pixelated'])
                    ->size(40),
                ToggleColumn::make('published')
                    ->label(__('Published')),
            ])
            ->actions([
                DeleteAction::make()
                    ->before(function (DeleteAction $action, WebsiteDrawBadge $record) {
                        $badgeCode = pathinfo($record->badge_path, PATHINFO_FILENAME);

                        // Unpublish if published
                        if ($record->published) {
                            DB::table('users_badges')
                                ->where('user_id', $record->user_id)
                                ->where('badge_code', $badgeCode)
                                ->delete();
                        }

                        // Remove from JSON
                        $filePath = DB::table('website_settings')->where('key', 'nitro_external_texts_file')->value('value');

                        if ($filePath && file_exists($filePath) && is_writable($filePath)) {
                            $json = json_decode(file_get_contents($filePath), true);
                            unset($json["badge_name_{$badgeCode}"]);
                            unset($json["badge_desc_{$badgeCode}"]);
                            file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }

                        // Delete the badge file from the filesystem
                        $badgePath = $record->badge_path;
                        if ($badgePath && file_exists($badgePath)) {
                            unlink($badgePath); // Remove the file
                        }
                    }),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->before(function (DeleteBulkAction $action, $records) {
                        foreach ($records as $record) {
                            $badgeCode = pathinfo($record->badge_path, PATHINFO_FILENAME);

                            if ($record->published) {
                                DB::table('users_badges')
                                    ->where('user_id', $record->user_id)
                                    ->where('badge_code', $badgeCode)
                                    ->delete();
                            }

                            $filePath = DB::table('website_settings')->where('key', 'nitro_external_texts_file')->value('value');

                            if ($filePath && file_exists($filePath) && is_writable($filePath)) {
                                $json = json_decode(file_get_contents($filePath), true);
                                unset($json["badge_name_{$badgeCode}"]);
                                unset($json["badge_desc_{$badgeCode}"]);
                                file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                            }

                            $badgePath = $record->badge_path;
                            if ($badgePath && file_exists($badgePath)) {
                                unlink($badgePath);
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteDrawBadge::route('/'),
            'edit' => Pages\EditWebsiteDrawBadge::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}