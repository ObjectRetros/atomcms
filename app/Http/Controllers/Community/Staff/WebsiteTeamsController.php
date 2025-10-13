<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Models\Community\Staff\WebsiteOpenPosition;
use Illuminate\View\View;

class WebsiteTeamsController extends Controller
{
    public function __invoke(): View
    {
        $positions = WebsiteOpenPosition::query()
            ->where('position_kind', 'team')
            ->whereNotNull('team_id')
            ->with('team')
            ->whereHas('team')
            ->latest()
            ->get();

        return view('community.team-applications', [
            'positions' => $positions,
        ]);
    }
}
