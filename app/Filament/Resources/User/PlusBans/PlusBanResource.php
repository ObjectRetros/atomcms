<?php

namespace App\Filament\Resources\User\PlusBans;

use App\Filament\Concerns\RequiresEmulatorDriver;
use App\Filament\Concerns\TranslatableResource;
use App\Filament\Resources\User\PlusBans\Pages\ManagePlusBans;
use App\Models\Plus\PlusBan;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Ban management on the Plus EMU schema; the Arcturus counterpart is
 * BanResource. Enforcement on the site is driver-agnostic via BanRepository.
 */
class PlusBanResource extends Resource
{
    use RequiresEmulatorDriver;
    use TranslatableResource;

    protected static ?string $model = PlusBan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $slug = 'user-management/plus-bans';

    protected static ?int $navigationSort = 1;

    public static string $translateIdentifier = 'bans';

    protected static function requiredEmulatorDriver(): string
    {
        return 'plus';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bantype')
                    ->native(false)
                    ->label(__('filament::resources.inputs.type'))
                    ->required()
                    ->options([
                        'user' => __('filament::resources.common.Account'),
                        'ip' => __('filament::resources.common.IP'),
                        'machine' => __('filament::resources.common.Machine'),
                    ])
                    ->columnSpanFull(),

                TextInput::make('value')
                    ->label(__('filament::resources.columns.value'))
                    ->helperText(__('The username, IP address or machine id being banned'))
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('reason')
                    ->label(__('filament::resources.inputs.reason'))
                    ->columnSpanFull(),

                DateTimePicker::make('expire')
                    ->native(false)
                    ->label(__('filament::resources.inputs.expires_at'))
                    ->displayFormat('Y-m-d H:i')
                    ->format('U')
                    ->required()
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

                TextColumn::make('bantype')
                    ->badge()
                    ->label(__('filament::resources.columns.type'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'user' => __('filament::resources.common.Account'),
                        'ip' => __('filament::resources.common.IP'),
                        'machine' => __('filament::resources.common.Machine'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'primary',
                        'ip' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('value')
                    ->label(__('filament::resources.columns.value'))
                    ->searchable(),

                TextColumn::make('reason')
                    ->label(__('filament::resources.columns.reason'))
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('added_by')
                    ->label(__('filament::resources.columns.by'))
                    ->searchable(),

                TextColumn::make('added_date')
                    ->label(__('filament::resources.columns.banned_at'))
                    ->formatStateUsing(fn ($state): string => $state ? date('Y-m-d H:i', (int) $state) : '-'),

                TextColumn::make('expire')
                    ->label(__('filament::resources.columns.expires_at'))
                    ->formatStateUsing(fn ($state): string => $state == 0 ? __('filament::resources.common.Never') : date('Y-m-d H:i', (int) $state)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePlusBans::route('/'),
        ];
    }
}
