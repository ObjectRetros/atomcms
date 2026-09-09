<?php

namespace App\Services\Community;

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\Stat;
use App\Enums\CurrencyTypes;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CommunityReadService
{
    public function __construct(private readonly CurrencyRepository $currencies, private readonly PlayerStatsRepository $stats, private readonly StaffService $staff) {}

    /** @return array<string, mixed> */
    public function leaderboards(): array
    {
        $staffIds = $this->staff->fetchEmployeeIds();

        return [
            'credits' => $this->currencies->topBy(CurrencyTypes::Credits, 9, $staffIds),
            'duckets' => $this->currencies->topBy(CurrencyTypes::Duckets, 9, $staffIds),
            'diamonds' => $this->currencies->topBy(CurrencyTypes::Diamonds, 9, $staffIds),
            'mostOnline' => $this->stats->supports(Stat::OnlineTime) ? $this->stats->topBy(Stat::OnlineTime, 9, $staffIds) : null,
            'respectsReceived' => $this->stats->topBy(Stat::RespectsReceived, 9, $staffIds),
            'achievementScores' => $this->stats->topBy(Stat::AchievementScore, 9, $staffIds),
        ];
    }

    /** @return Collection<int, WebsiteOpenPosition> */
    public function positions(string $kind): Collection
    {
        $relation = $kind === 'team' ? 'team' : 'permission';
        $column = $kind === 'team' ? 'team_id' : 'permission_id';

        return WebsiteOpenPosition::query()->where('position_kind', $kind)->whereNotNull($column)->canApply()->with($relation)->whereHas($relation)->latest()->get();
    }

    /**
     * @param  array<int, int>  $teamIds
     *
     * @return array<int, string>
     */
    public function teamApplicationStatuses(User $actor, array $teamIds): array
    {
        return WebsiteStaffApplications::query()
            ->where('user_id', $actor->id)
            ->whereIn('team_id', $teamIds)
            ->pluck('status', 'team_id')
            ->all();
    }

    public function position(WebsiteOpenPosition $position): WebsiteOpenPosition
    {
        abort_unless(in_array($position->position_kind, ['rank', 'team'], true), 404);
        $relation = $position->position_kind === 'team' ? 'team' : 'permission';
        $position->loadMissing($relation);
        abort_unless($position->getRelation($relation) !== null && $position->isAcceptingApplications(), 404);

        return $position;
    }
}
