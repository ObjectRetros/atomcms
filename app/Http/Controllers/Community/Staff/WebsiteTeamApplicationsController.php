<?php

namespace App\Http\Controllers\Community\Staff;

use App\Actions\Community\SubmitStaffApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffApplicationFormRequest;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Services\Community\CommunityReadService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteTeamApplicationsController extends Controller
{
    public function index(Request $request): View
    {
        $positions = app(CommunityReadService::class)->positions('team');

        $userAppStatuses = [];
        $user = $request->user();

        if ($user !== null) {
            $teamIds = $positions->pluck('team_id')->filter()->unique()->all();

            $userAppStatuses = app(CommunityReadService::class)->teamApplicationStatuses($user, $teamIds);
        }

        return view('community.team-applications', [
            'positions' => $positions,
            'userAppStatuses' => $userAppStatuses,
        ]);
    }

    public function show(WebsiteOpenPosition $position): View
    {
        abort_unless($position->position_kind === 'team', 404);

        app(CommunityReadService::class)->position($position);

        return view('community.team-apply', [
            'position' => $position,
        ]);
    }

    public function store(
        StaffApplicationFormRequest $request,
        WebsiteOpenPosition $position,
        SubmitStaffApplication $applications,
    ): RedirectResponse {
        abort_unless($position->position_kind === 'team', 404);
        $applications->forPosition(AuthenticatedUser::from($request), $position, $request->string('content')->toString());

        return redirect()
            ->route('team-applications.index')
            ->with('success', __('Your application has been submitted!'));
    }
}
