<?php

namespace App\Http\Controllers\Community;

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\Stat;
use App\Enums\CurrencyTypes;
use App\Http\Controllers\Controller;
use App\Services\Community\StaffService;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    private const SIZE = 9;

    /**
     * @var array<int, int>
     */
    private array $staffIds;

    public function __construct(private readonly StaffService $staffService)
    {
        $this->staffIds = $this->staffService->fetchEmployeeIds();
    }

    public function __invoke(CurrencyRepository $currencies, PlayerStatsRepository $stats): View
    {
        return view('leaderboard', [
            'credits' => $currencies->topBy(CurrencyTypes::Credits, self::SIZE, $this->staffIds),
            'duckets' => $currencies->topBy(CurrencyTypes::Duckets, self::SIZE, $this->staffIds),
            'diamonds' => $currencies->topBy(CurrencyTypes::Diamonds, self::SIZE, $this->staffIds),
            'mostOnline' => $stats->topBy(Stat::OnlineTime, self::SIZE, $this->staffIds),
            'respectsReceived' => $stats->topBy(Stat::RespectsReceived, self::SIZE, $this->staffIds),
            'achievementScores' => $stats->topBy(Stat::AchievementScore, self::SIZE, $this->staffIds),
        ]);
    }
}
