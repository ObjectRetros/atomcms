<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\Community\Staff\WebsiteStaffApplications;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffApplicationsController extends Controller
{
    public function index(): View
    {
        $positions = WebsiteOpenPosition::query()
            ->where('position_kind', 'rank')
            ->whereNotNull('permission_id')
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
        abort_unless($position->permission, 404);

        return view('community.staff-apply', compact('position'));
    }

    public function store(Request $request, WebsiteOpenPosition $position)
    {
        abort_unless($position->position_kind === 'rank', 404);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:10'],
        ]);

        $user = $request->user();

        if ($user->hasAppliedForPosition($position->permission_id)) {
            return back()->withErrors([
                'content' => __('You have already applied for this position.'),
            ])->withInput();
        }

        WebsiteStaffApplications::create([
            'user_id' => $user->id,
            'rank_id' => $position->permission_id,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('staff-applications.index')
            ->with('status', __('Your application has been submitted!'));
    }
}
