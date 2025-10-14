<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteTeamApplicationsController extends Controller
{
    public function index(): View
    {
        $positions = \App\Models\Community\Staff\WebsiteOpenPosition::query()
            ->where('position_kind', 'team')
            ->whereNotNull('team_id')
            ->with('team')
            ->whereHas('team')
            ->latest()
            ->get();

        $userAppStatuses = [];
        if (auth()->check()) {
            $teamIds = $positions->pluck('team_id')->filter()->unique()->all();

            $userAppStatuses = \App\Models\Community\Staff\WebsiteStaffApplications::query()
                ->where('user_id', auth()->id())
                ->whereIn('team_id', $teamIds)
                ->pluck('status', 'team_id')
                ->toArray();
        }

        return view('community.team-applications', [
            'positions' => $positions,
            'userAppStatuses' => $userAppStatuses,
        ]);
    }

    public function show(WebsiteOpenPosition $position): View
    {
        abort_unless($position->position_kind === 'team', 404);

        $position->loadMissing('team');

        return view('community.team-apply', [
            'position' => $position,
        ]);
    }

    public function store(Request $request, WebsiteOpenPosition $position)
    {
        abort_unless($position->position_kind === 'team', 404);

        $request->validate([
            'content' => ['required', 'string', 'min:10'],
        ]);

        $user = $request->user();

        if ($user->hasAppliedForTeam($position->team_id)) {
            return back()->withErrors([
                'content' => __('You have already applied for this team.'),
            ]);
        }

        WebsiteStaffApplications::create([
            'user_id' => $user->id,
            'team_id' => $position->team_id,
            'content' => $request->string('content'),
        ]);

        return redirect()
            ->route('team-applications.index')
            ->with('status', __('Your application has been submitted!'));
    }
}
