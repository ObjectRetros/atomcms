<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffApplicationFormRequest;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Services\Community\StaffApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffApplicationsController extends Controller
{
    public function __construct(
        private readonly StaffApplicationService $applicationService,
    ) {}

    public function index(): View
    {
        return view('community.staff-applications', [
            'positions' => $this->applicationService->fetchOpenPositions(),
        ]);
    }

    public function show(WebsiteOpenPosition $position): View
    {
        $this->ensurePositionIsValid($position);

        return view('community.staff-apply', compact('position'));
    }

    public function store(StaffApplicationFormRequest $request, WebsiteOpenPosition $position): RedirectResponse
    {
        $this->ensurePositionIsValid($position);

        $user = $request->user();

        if ($user->hasAppliedForPosition($position->permission_id)) {
            return back()->withErrors([
                'content' => __('You have already applied for this position.'),
            ])->withInput();
        }

        $this->applicationService->storeApplication(
            $user,
            $position->permission_id,
            $request->validated()['content'],
        );

        return redirect()
            ->route('staff-applications.index')
            ->with('status', __('Your application has been submitted!'));
    }

    private function ensurePositionIsValid(WebsiteOpenPosition $position): void
    {
        abort_unless($position->position_kind === 'rank', 404);
        $position->loadMissing('permission');
        abort_unless($position->permission, 404);
    }
}
