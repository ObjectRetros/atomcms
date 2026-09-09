<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientLaunchService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Serve a game client. The route supplies which one via a default for the
     * "client" parameter (client.nitro or client.flash).
     */
    public function __invoke(Request $request, string $client): View
    {
        $user = AuthenticatedUser::from($request);

        $view = match ($client) {
            'flash' => 'client.flash',
            default => 'client.nitro',
        };

        return view($view, [
            'sso' => app(ClientLaunchService::class)->ticket($user, $request->ip() ?: 'unknown'),
        ]);
    }
}
