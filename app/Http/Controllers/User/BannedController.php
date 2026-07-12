<?php

namespace App\Http\Controllers\User;

use App\Emulator\Contracts\BanRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BannedController extends Controller
{
    public function __invoke(BanRepository $bans): View
    {
        $user = Auth::user();

        $ban = $bans->activeIpBan((string) request()->ip())
            ?? ($user !== null ? $bans->activeAccountBan($user) : null);

        return view('banned', [
            'ban' => $ban,
        ]);
    }
}
