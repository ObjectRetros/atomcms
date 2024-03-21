<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCurrency;
use App\Models\UserSetting;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    protected array $staffIds = [];

    public function __construct()
    {
        $this->staffIds = User::select('id')
            ->where('rank', '>=', setting('min_staff_rank'))
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function __invoke(): View
    {
        $topCredits = User::query()
            ->whereNotIn('id', $this->staffIds)
            ->orderByDesc('credits')
            ->take(9)
            ->get();

        $getUserCurrency = fn($type) => UserCurrency::query()
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
