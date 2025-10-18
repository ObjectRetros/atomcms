<?php

namespace App\Filament\Resources\Hotel\StaffApplications;

use App\Filament\Resources\Hotel\StaffApplications\Pages\ListStaffApplications;
use App\Models\Community\Staff\WebsiteStaffApplications;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffApplicationResource extends Resource
{
    protected static ?string $model = WebsiteStaffApplications::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->relationship('user', 'username')
                ->required()
                ->searchable(),

            Select::make('rank_id')
                ->label('Rank')
                ->relationship('rank', 'rank_name')
                ->searchable()
                ->nullable(),

            Select::make('team_id')
                ->label('Team')
                ->relationship('team', 'rank_name')
                ->searchable()
                ->nullable(),

            Textarea::make('content')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('applied_for')
                    ->label('Applied For')
                    ->state(fn (WebsiteStaffApplications $record) => $record->team_id
                        ? ($record->team->rank_name ?? '-')
                        : ($record->rank->rank_name ?? '-'))
                    ->searchable(query: function ($query, string $search) {
                        $query
                            ->orWhereHas('rank', fn ($q) => $q->where('rank_name', 'like', "%{$search}%"))
                            ->orWhereHas('team', fn ($q) => $q->where('rank_name', 'like', "%{$search}%"));
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst($state ?? 'pending'))
                    ->color(fn (?string $state) => [
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    ][$state ?? 'pending'] ?? 'gray')
                    ->sortable(),

                TextColumn::make('content')->limit(50)->wrap()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                Action::make('approveTeam')
                    ->label('Approve to Team')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (WebsiteStaffApplications $r) => filled($r->team_id) && ($r->status === 'pending' || is_null($r->status)))
                    ->requiresConfirmation()
                    ->modalHeading('Approve to Team')
                    ->modalDescription(function (WebsiteStaffApplications $r): string {
                        $user = $r->user;
                        $targetTeam = optional($r->team)->rank_name ?? '—';
                        $currentTeam = optional($user?->team)->rank_name;

                        if ($currentTeam && $user?->team_id !== $r->team_id) {
                            return "This user is currently in '{$currentTeam}'. Approving will move them to '{$targetTeam}'. Continue?";
                        }

                        return "Approve this application and assign the user to '{$targetTeam}'?";
                    })
                    ->action(function (WebsiteStaffApplications $r) {
                        $user = $r->user;
                        $team = $r->team;

                        if (! $user || ! $team) {
                            Notification::make()
                                ->danger()->title('Unable to approve')
                                ->body('Missing user or team on this application.')
                                ->send();

                            return;
                        }

                        if ((int) $user->team_id !== (int) $team->id) {
                            $user->update(['team_id' => $team->id]);
                        }

                        $r->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejected_by' => null,
                            'rejected_at' => null,
                        ]);

                        Notification::make()
                            ->success()->title('Approved')
                            ->body("{$user->username} has been added to '{$team->rank_name}'.")
                            ->send();
                    }),

                Action::make('rejectTeam')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (WebsiteStaffApplications $r) => filled($r->team_id) && in_array($r->status, ['pending', 'approved', null], true))
                    ->requiresConfirmation()
                    ->modalHeading('Reject Application')
                    ->modalDescription(function (WebsiteStaffApplications $r): string {
                        $user = $r->user;
                        $teamName = optional($r->team)->rank_name ?? '—';

                        if ($r->status === 'approved') {
                            return "This will mark the application as rejected and remove {$user->username} from '{$teamName}' (if still on it). Continue?";
                        }

                        return 'This will mark the application as rejected. Continue?';
                    })
                    ->action(function (WebsiteStaffApplications $r) {
                        $user = $r->user;
                        $team = $r->team;

                        if (! $user || ! $team) {
                            Notification::make()
                                ->danger()->title('Unable to reject')
                                ->body('Missing user or team on this application.')
                                ->send();

                            return;
                        }

                        if ($r->status === 'approved' && (int) $user->team_id === (int) $team->id) {
                            $user->update(['team_id' => null]);
                        }

                        $r->update([
                            'status' => 'rejected',
                            'rejected_by' => auth()->id(),
                            'rejected_at' => now(),
                        ]);

                        Notification::make()
                            ->success()->title('Rejected')
                            ->body($r->status === 'approved'
                                ? "{$user->username} has been removed from '{$team->rank_name}' and the application marked as rejected."
                                : 'Application has been marked as rejected.')
                            ->send();
                    }),

                Action::make('reopen')
                    ->label('Re-open')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (WebsiteStaffApplications $r) => $r->status === 'rejected')
                    ->requiresConfirmation()
                    ->modalHeading('Re-open Application')
                    ->modalDescription('This will set the application status back to pending.')
                    ->action(function (WebsiteStaffApplications $r) {
                        $r->update([
                            'status' => 'pending',
                            'rejected_by' => null,
                            'rejected_at' => null,
                        ]);

                        Notification::make()
                            ->success()->title('Re-opened')
                            ->body('Application status set to pending.')
                            ->send();
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffApplications::route('/'),
        ];
    }
}
