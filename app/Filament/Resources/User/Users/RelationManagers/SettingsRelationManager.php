<?php

namespace App\Filament\Resources\User\Users\RelationManagers;

use App\Filament\Traits\TranslatableResource;
use Carbon\CarbonInterval;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsRelationManager extends RelationManager
{
    use TranslatableResource;

    protected static string $relationship = 'settings';

    protected static string $translateIdentifier = 'settings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->schema([
                        Tab::make(__('filament::resources.tabs.Account Data'))
                            ->schema([
                                TextInput::make('achievement_score')
                                    ->label(__('filament::resources.inputs.achievement_score'))
                                    ->disabled()
                                    ->required(),

                                TextInput::make('respects_received')
                                    ->label(__('filament::resources.inputs.respects_received'))
                                    ->disabled()
                                    ->required(),

                                Select::make('can_trade')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.can_trade'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('block_following')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.block_following'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('block_friendrequests')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.block_friendrequests'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('block_roominvites')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.block_roominvites'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                TextInput::make('max_rooms')
                                    ->label(__('filament::resources.inputs.max_rooms'))
                                    ->numeric()
                                    ->required(),

                                TextInput::make('max_friends')
                                    ->label(__('filament::resources.inputs.max_friends'))
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(['sm' => 2]),

                        Tab::make(__('filament::resources.tabs.Extra Settings'))
                            ->schema([
                                Select::make('old_chat')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.old_chat'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('block_camera_follow')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.block_camera_follow'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('ignore_bots')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.ignore_bots'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),

                                Select::make('ignore_pets')
                                    ->native(false)
                                    ->label(__('filament::resources.inputs.ignore_pets'))
                                    ->options([
                                        '0' => __('filament::resources.common.No'),
                                        '1' => __('filament::resources.common.Yes'),
                                    ])
                                    ->required(),
                            ])->columns(['sm' => 2]),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('achievement_score')
                    ->label(__('filament::resources.columns.achievement_score'))
                    ->toggleable(),

                TextColumn::make('respects_received')
                    ->label(__('filament::resources.columns.respects_received'))
                    ->toggleable(),

                TextColumn::make('online_time')
                    ->label(__('filament::resources.columns.online_time'))
                    ->formatStateUsing(fn (string $state) => __(':m minutes', ['m' => round(CarbonInterval::seconds($state)->totalMinutes)]))
                    ->toggleable(),

                IconColumn::make('can_trade')
                    ->label(__('filament::resources.columns.can_trade'))
                    ->icon(fn (string $state) => $state === '1' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'success' => '1',
                        'danger' => '0',
                    ]),

                IconColumn::make('can_change_name')
                    ->label(__('filament::resources.columns.can_change_name'))
                    ->icon(fn (string $state) => $state === '1' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->colors([
                        'success' => '1',
                        'danger' => '0',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('helper')
                    ->label('Settings Tip')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->tooltip('You can only change the offline user settings.')
                    ->extraAttributes(['style' => 'cursor: default !important']),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->online),
            ])
            ->toolbarActions([]);
    }
}
