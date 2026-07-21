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

    public function __invoke(
        CurrencyRepository $currencies,
        PlayerStatsRepository $stats,
        StaffService $staffService,
    ): View {
        $staffIds = $staffService->fetchEmployeeIds();

        return view('leaderboard', [
            'credits' => $currencies->topBy(CurrencyTypes::Credits, self::SIZE, $staffIds),
            'duckets' => $currencies->topBy(CurrencyTypes::Duckets, self::SIZE, $staffIds),
            'diamonds' => $currencies->topBy(CurrencyTypes::Diamonds, self::SIZE, $staffIds),
            'mostOnline' => $stats->topBy(Stat::OnlineTime, self::SIZE, $staffIds),
            'respectsReceived' => $stats->topBy(Stat::RespectsReceived, self::SIZE, $staffIds),
            'achievementScores' => $stats->topBy(Stat::AchievementScore, self::SIZE, $staffIds),
        ]);
    }
}
