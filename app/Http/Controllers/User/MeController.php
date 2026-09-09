<?php

namespace App\Http\Controllers\User;

use App\Emulator\Contracts\RankRepository;
use App\Http\Controllers\Controller;
use App\Services\Articles\ArticleService;
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
            'articles' => app(ArticleService::class)->latestWithAuthor(5),
        ]);
    }
}
