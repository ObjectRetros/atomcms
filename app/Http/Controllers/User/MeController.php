<?php

namespace App\Http\Controllers\User;

use App\Emulator\Contracts\RankRepository;
use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use App\Support\AuthenticatedUser;
use Illuminate\View\View;

class MeController extends Controller
{
    public function __invoke(): View
    {
        $user = AuthenticatedUser::current();

        return view('user.me', [
            'onlineFriends' => $user->getOnlineFriends(),
            // The rank model and its displayable columns differ per driver.
            'user' => $user->load(['permission' => fn ($query) => app(RankRepository::class)->forDisplay($query)]),
            'articles' => WebsiteArticle::whereHas('user')->with('user:id,username,look')->latest()->take(5)->get(),
        ]);
    }
}
