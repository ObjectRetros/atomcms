<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
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

        $user->update([
            'ip_current' => $request->ip(),
        ]);

        return view("client.{$client}", [
            'sso' => $user->ssoTicket(),
        ]);
    }
}
