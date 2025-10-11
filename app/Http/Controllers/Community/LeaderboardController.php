<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Game\Player\UserCurrency;
use App\Models\Game\Player\UserSetting;
use App\Models\User;
use App\Services\Community\StaffService;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    protected array $staffIds = [];

    public function __construct(private readonly StaffService $staffService)
    {
        $this->staffIds = $this->staffService->fetchEmployeeIds();
    }

    public function __invoke(): View
    {
        $topCredits = User::query()
            ->whereNotIn('id', $this->staffIds)
            ->orderByDesc('credits')
            ->take(9)
            ->get();

        $getUserCurrency = fn ($type) => UserCurrency::query()
            ->whereNotIn('user_id', $this->staffIds)
            ->where('type', $type)
            ->orderByDesc('amount')
            ->take(9)
            ->with('user:id,username,look')
            ->get();

        return view('leaderboard', [
            'credits' => $topCredits,
            'duckets' => $getUserCurrency(0),
            'diamonds' => $getUserCurrency(5),
            'mostOnline' => $this->retrieveSettings('online_time'),
            'respectsReceived' => $this->retrieveSettings('respects_received'),
            'achievementScores' => $this->retrieveSettings('achievement_score'),
        ]);
    }

    private function retrieveSettings($column)
    {
        return UserSetting::select('user_id', $column)
            ->whereNotIn('user_id', $this->staffIds)
            ->orderByDesc($column)
            ->take(9)
            ->with('user:id,username,look')
            ->get();
    }
}
