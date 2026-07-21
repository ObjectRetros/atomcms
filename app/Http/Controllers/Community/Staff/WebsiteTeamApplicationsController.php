<?php

namespace App\Http\Controllers\Community\Staff;

use App\Actions\Community\SubmitStaffApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffApplicationFormRequest;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteTeamApplicationsController extends Controller
{
    public function index(Request $request): View
    {
        $positions = WebsiteOpenPosition::query()
            ->where('position_kind', 'team')
            ->whereNotNull('team_id')
            ->canApply()
            ->with('team')
            ->whereHas('team')
            ->latest()
            ->get();

        $userAppStatuses = [];
        $user = $request->user();

        if ($user !== null) {
            $teamIds = $positions->pluck('team_id')->filter()->unique()->all();

            $userAppStatuses = WebsiteStaffApplications::query()
                ->where('user_id', $user->id)
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
        abort_unless($position->team !== null && $position->isAcceptingApplications(), 404);

        return view('community.team-apply', [
            'position' => $position,
        ]);
    }

    public function store(
        StaffApplicationFormRequest $request,
        WebsiteOpenPosition $position,
        SubmitStaffApplication $applications,
    ): RedirectResponse {
        $position->loadMissing('team');
        abort_unless(
            $position->position_kind === 'team'
            && $position->team !== null
            && $position->team_id !== null
            && $position->isAcceptingApplications(),
            404,
        );

        $applications->forTeam(
            AuthenticatedUser::from($request),
            $position->team_id,
            $request->string('content')->toString(),
        );

        return redirect()
            ->route('team-applications.index')
            ->with('success', __('Your application has been submitted!'));
    }
}
