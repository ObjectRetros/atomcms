<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use App\Services\Help\TicketService;
use Illuminate\View\View;

class WebsiteRulesController extends Controller
{
    public function __invoke(): View
    {
        return view('rules', [
            'categories' => app(TicketService::class)->rules(),
        ]);
    }
}
