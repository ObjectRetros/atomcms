<?php

namespace App\Filament\Resources\User\AdaIpBans;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatesNavigationGroup;
use App\Filament\Resources\User\AdaIpBans\Pages\ManageAdaIpBans;
use App\Models\Ada\AdaIpBan;
use App\Support\AuthenticatedUser;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdaIpBanResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatesNavigationGroup;

    protected static ?string $model = AdaIpBan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-no-symbol';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'IP bans';

    protected static ?string $slug = 'user-management/ada-ip-bans';

    protected static function requiredEmulatorDriver(): string
    {
        return 'ada';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ip_address')->ip()->required(),
            Textarea::make('reason')->required()->columnSpanFull(),
            DateTimePicker::make('expires_at')->native(false)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('ip_address')->searchable(),
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
        return ['index' => ManageAdaIpBans::route('/')];
    }
}
