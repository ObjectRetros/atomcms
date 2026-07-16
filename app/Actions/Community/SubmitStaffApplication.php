<?php

namespace App\Actions\Community;

use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitStaffApplication
{
    public function forRank(User $user, int $rankId, string $content): WebsiteStaffApplications
    {
        return $this->submit($user, 'rank', $rankId, $content);
    }

    public function forTeam(User $user, int $teamId, string $content): WebsiteStaffApplications
    {
        return $this->submit($user, 'team', $teamId, $content);
    }

    private function submit(User $user, string $kind, int $targetId, string $content): WebsiteStaffApplications
    {
        $application = WebsiteStaffApplications::query()->createOrFirst(
            ['application_key' => self::key($kind, $user->id, $targetId)],
            [
                'user_id' => $user->id,
                'rank_id' => $kind === 'rank' ? $targetId : null,
                'team_id' => $kind === 'team' ? $targetId : null,
                'content' => $content,
            ],
        );

        if (! $application->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'content' => $kind === 'rank'
                    ? __('You have already applied for this position.')
                    : __('You have already applied for this team.'),
            ]);
        }

        return $application;
    }

    public static function key(string $kind, int $userId, int $targetId): string
    {
        return "{$kind}:{$userId}:{$targetId}";
    }
}
