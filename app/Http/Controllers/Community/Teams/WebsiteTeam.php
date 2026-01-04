<?php

use App\Models\Community\Teams\WebsiteTeam;

public function __invoke(): View
{
    $employees = WebsiteTeam::query()
        ->where('hidden_rank', false)            // show only visible teams
        ->with(['users' => fn ($q) => $q
            ->select(['id','username','look','motto','team_id']) // whatever your card needs
            ->orderBy('username')
        ])
        ->orderBy('rank_name')
        ->get();

    return view('community.teams', compact('employees'));
}
