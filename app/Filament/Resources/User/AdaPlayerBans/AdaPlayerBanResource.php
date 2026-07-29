<?php

namespace App\Filament\Resources\User\AdaPlayerBans;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\User\AdaPlayerBans\Pages\ManageAdaPlayerBans;
use App\Models\Ada\AdaPlayerBan;
use App\Models\User;
use App\Support\AuthenticatedUser;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaPlayerBanResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaPlayerBan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Player bans';

    protected static ?string $slug = 'user-management/ada-player-bans';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // Searched server-side: a hotel's user table is far too large to
            // pull into the form as a static option list.
            Select::make('player_id')
                ->label(__('filament::resources.columns.username'))
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => User::query()
                    ->where('username', 'like', "{$search}%")
                    ->orderBy('username')
                    ->limit(50)
                    ->pluck('username', 'id')
                    ->all())
                ->getOptionLabelUsing(fn ($value): ?string => User::query()->whereKey($value)->value('username'))
                ->required(),
            Textarea::make('reason')->required()->columnSpanFull(),
            DateTimePicker::make('expires_at')->native(false)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('player.username')->label(__('filament::resources.columns.username'))->searchable(),
                TextColumn::make('creator.username')->label(__('filament::resources.columns.by')),
                TextColumn::make('reason')->limit(40)->searchable(),
                TextColumn::make('created_at')->dateTime(),
                TextColumn::make('expires_at')->dateTime()->placeholder(__('filament::resources.common.Never')),
            ])
            ->headerActions([
                CreateAction::make()->mutateDataUsing(fn (array $data): array => array_merge($data, [
                    'creator_id' => AuthenticatedUser::current()->id,
                    'created_at' => now(),
                ])),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAdaPlayerBans::route('/')];
    }
}
