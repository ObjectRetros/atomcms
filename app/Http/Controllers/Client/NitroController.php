<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NitroController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService,
    ) {}

    public function __invoke(): View
    {
        $sso = $this->clientService->prepareClientSession(Auth::user(), request());

        return view('client.nitro', compact('sso'));
    }
}
