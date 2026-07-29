<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use App\Models\Miscellaneous\CameraWeb;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserReferralController extends Controller
{
    public function __invoke(User $user): View|RedirectResponse
    {
        if (setting('disable_registration') === '1') {
            return to_route('welcome')->withErrors(['register' => __('Registration is currently disabled.')]);
        }

        // The same sidebar data Fortify's register view is configured with in
        // FortifyServiceProvider::authPageData().
        return view('auth.register', [
            'referral_code' => $user->referral_code,
            'articles' => WebsiteArticle::latest('id')->take(4)->has('user')->with('user:id,username,look')->get(),
            'photos' => CameraWeb::latest('id')->take(2)->with('user:id,username,look')->get(),
        ]);
    }
}
