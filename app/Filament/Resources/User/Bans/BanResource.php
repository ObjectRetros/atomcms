<?php

namespace App\Filament\Resources\User\Bans;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatableResource;
use App\Filament\Resources\User\Bans\Pages\ManageBans;
use App\Filament\Support\TruncatedTooltip;
use App\Filament\Tables\Columns\UserAvatarColumn;
use App\Models\User\Ban;
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
use Illuminate\Database\Eloquent\Builder;

class BanResource extends Resource
{
    // Bound to the Arcturus bans schema; PlusBanResource covers Plus.
    use RequiresEmulatorDriver;
    use TranslatableResource;

    protected static function requiredEmulatorDriver(): string
    {
        return 'arcturus';
    }

    protected static ?string $model = Ban::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $slug = 'user-management/bans';

    protected static ?int $navigationSort = 1;

    public static string $translateIdentifier = 'bans';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('ban_reason')
                    ->label(__('filament::resources.inputs.reason'))
                    ->columnSpanFull(),

                Select::make('type')
                    ->native(false)
                    ->label(__('filament::resources.inputs.type'))
                    ->columnSpanFull()
                    ->options([
                        'account' => __('filament::resources.common.Account'),
                        'ip' => __('filament::resources.common.IP'),
                        'machine' => __('filament::resources.common.Machine'),
                        'super' => __('filament::resources.common.Super'),
                    ]),

                DateTimePicker::make('ban_expire')
                    ->native(false)
                    ->label(__('filament::resources.inputs.expires_at'))
                    ->displayFormat('Y-m-d H:i')
                    ->format('U')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id')),

                UserAvatarColumn::make('avatar')
                    ->toggleable()
                    ->pointer('user.look')
                    ->label(__('filament::resources.columns.avatar'))
                    ->options('&size=m&head_direction=3&gesture=sml&headonly=1'),

                TextColumn::make('user.username')
                    ->label(__('filament::resources.columns.username'))
                    ->searchable(),

                TextColumn::make('staff.username')
                    ->label(__('filament::resources.columns.by'))
                    ->searchable(),

                TextColumn::make('ban_reason')
                    ->label(__('filament::resources.columns.reason'))
                    ->tooltip(fn (TextColumn $column): ?string => TruncatedTooltip::of($column))
                    ->limit(15)
                    ->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->label(__('filament::resources.columns.type'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'account' => __('filament::resources.common.Account'),
                        'ip' => __('filament::resources.common.IP'),
                        'machine' => __('filament::resources.common.Machine'),
                        'super' => __('filament::resources.common.Super'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'account' => 'primary',
                        'ip' => 'success',
                        'machine' => 'primary',
                        'super' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('timestamp')
                    ->label(__('filament::resources.columns.banned_at'))
                    ->date('Y-m-d H:i'),

                TextColumn::make('ban_expire')
                    ->label(__('filament::resources.columns.expires_at'))
                    ->formatStateUsing(fn (string $state): string => $state === '0' ? __('filament::resources.common.Never') : date('Y-m-d H:i', (int) $state)),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'staff']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBans::route('/'),
        ];
    }
}
