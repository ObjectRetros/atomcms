<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Services\Help\TicketService;
use Illuminate\View\View;

class HelpCenterController extends Controller
{
    public function __invoke(): View
    {
        return view('help-center.index', [
            'categories' => app(TicketService::class)->categories(),
        ]);
    }
}
