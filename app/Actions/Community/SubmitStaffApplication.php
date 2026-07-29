<?php

namespace App\Actions\Community;

use App\Enums\StaffApplicationKind;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitStaffApplication
{
    public function execute(User $user, StaffApplicationKind $kind, int $targetId, string $content): WebsiteStaffApplications
    {
        $application = WebsiteStaffApplications::query()->createOrFirst(
            ['application_key' => self::key($kind, $user->id, $targetId)],
            [
                'user_id' => $user->id,
                'rank_id' => $kind === StaffApplicationKind::Rank ? $targetId : null,
                'team_id' => $kind === StaffApplicationKind::Team ? $targetId : null,
                'content' => $content,
            ],
        );

        if (! $application->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'content' => $kind === StaffApplicationKind::Rank
                    ? __('You have already applied for this position.')
                    : __('You have already applied for this team.'),
            ]);
        }

        return $application;
    }

    /**
     * Kept for the HTTP controllers; delegates to execute().
     */
    public function forRank(User $user, int $rankId, string $content): WebsiteStaffApplications
    {
        return $this->execute($user, StaffApplicationKind::Rank, $rankId, $content);
    }

    /**
     * Kept for the HTTP controllers; delegates to execute().
     */
    public function forTeam(User $user, int $teamId, string $content): WebsiteStaffApplications
    {
        return $this->execute($user, StaffApplicationKind::Team, $teamId, $content);
    }

    public static function key(StaffApplicationKind $kind, int $userId, int $targetId): string
    {
        return "{$kind->value}:{$userId}:{$targetId}";
    }
}
