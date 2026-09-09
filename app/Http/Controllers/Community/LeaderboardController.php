<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Services\Community\CommunityReadService;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(CommunityReadService $community): View
    {
        return view('leaderboard', $community->leaderboards());
    }
}
