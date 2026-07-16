<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Models\Help\WebsiteHelpCenterCategory;
use Illuminate\View\View;

class HelpCenterController extends Controller
{
    public function __invoke(): View
    {
        return view('help-center.index', [
            'categories' => WebsiteHelpCenterCategory::orderBy('position')->get(),
        ]);
    }
}
