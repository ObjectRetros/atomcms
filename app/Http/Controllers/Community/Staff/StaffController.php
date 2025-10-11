<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Services\Community\StaffService;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(private readonly StaffService $staffService) {}

    public function __invoke(): View
    {
        $employees = $this->staffService->fetchStaffPositions();

        return view('community.staff', [
            'employees' => $employees,
        ]);
    }
}
