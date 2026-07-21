<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Services\Community\StaffService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(private readonly StaffService $staffService) {}

    public function __invoke(Request $request): View
    {
        $employees = $this->staffService->fetchStaffPositions(AuthenticatedUser::from($request));

        return view('community.staff', [
            'employees' => $employees,
        ]);
    }
}
