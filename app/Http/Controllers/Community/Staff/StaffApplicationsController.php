<?php

namespace App\Http\Controllers\Community\Staff;

use App\Actions\Community\SubmitStaffApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffApplicationFormRequest;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffApplicationsController extends Controller
{
    public function index(): View
    {
        $positions = WebsiteOpenPosition::query()
            ->where('position_kind', 'rank')
            ->whereNotNull('permission_id')
            ->canApply()
            ->with('permission')
            ->whereHas('permission')
            ->latest()
            ->get();

        return view('community.staff-applications', compact('positions'));
    }

    public function show(WebsiteOpenPosition $position): View
    {
        abort_unless($position->position_kind === 'rank', 404);
        $position->loadMissing('permission');
        abort_unless($position->permission !== null && $position->isAcceptingApplications(), 404);

        return view('community.staff-apply', compact('position'));
    }

    public function store(
        StaffApplicationFormRequest $request,
        WebsiteOpenPosition $position,
        SubmitStaffApplication $applications,
    ): RedirectResponse {
        $position->loadMissing('permission');
        abort_unless(
            $position->position_kind === 'rank'
            && $position->permission !== null
            && $position->permission_id !== null
            && $position->isAcceptingApplications(),
            404,
        );

        $applications->forRank(
            AuthenticatedUser::from($request),
            $position->permission_id,
            $request->string('content')->toString(),
        );

        return redirect()
            ->route('staff-applications.index')
            ->with('success', __('Your application has been submitted!'));
    }
}
