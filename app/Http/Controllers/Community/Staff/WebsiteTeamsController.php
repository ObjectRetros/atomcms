<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Services\Community\TeamService;
use Illuminate\View\View;

class WebsiteTeamsController extends Controller
{
    public function __construct(private readonly TeamService $teamService) {}

    public function __invoke(): View
    {
        $employees = $this->teamService->fetchTeams();

        return view('community.teams', [
            'employees' => $employees,
        ]);
    }
}
