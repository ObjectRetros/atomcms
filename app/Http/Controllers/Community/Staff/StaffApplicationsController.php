<?php

namespace App\Http\Controllers\Community\Staff;

use App\Actions\Community\SubmitStaffApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffApplicationFormRequest;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Services\Community\CommunityReadService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffApplicationsController extends Controller
{
    public function index(): View
    {
        $positions = app(CommunityReadService::class)->positions('rank');

        return view('community.staff-applications', compact('positions'));
    }

    public function show(WebsiteOpenPosition $position): View
    {
        abort_unless($position->position_kind === 'rank', 404);
        app(CommunityReadService::class)->position($position);

        return view('community.staff-apply', compact('position'));
    }

    public function store(
        StaffApplicationFormRequest $request,
        WebsiteOpenPosition $position,
        SubmitStaffApplication $applications,
    ): RedirectResponse {
        abort_unless($position->position_kind === 'rank', 404);
        $applications->forPosition(AuthenticatedUser::from($request), $position, $request->string('content')->toString());

        return redirect()
            ->route('staff-applications.index')
            ->with('success', __('Your application has been submitted!'));
    }
}
