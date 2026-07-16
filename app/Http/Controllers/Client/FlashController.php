<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\AuthenticatedUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = AuthenticatedUser::from($request);

        $user->update([
            'ip_current' => $request->ip(),
        ]);

        return view('client.flash', [
            'sso' => $user->ssoTicket(),
        ]);
    }
}
