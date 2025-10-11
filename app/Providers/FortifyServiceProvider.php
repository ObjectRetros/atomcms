<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\DisableTwoFactorAuthentication;
use App\Actions\Fortify\RedirectIfTwoFactorConfirmed;
use App\Models\Articles\WebsiteArticle;
use App\Models\Miscellaneous\CameraWeb;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Laravel\Fortify\Actions\DisableTwoFactorAuthentication::class,
            DisableTwoFactorAuthentication::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('username') . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return view('auth.login', [
                'articles' => WebsiteArticle::latest('id')
                    ->take(4)
                    ->has('user')
                    ->with('user:id,username,look')
                    ->get(),
                'photos' => CameraWeb::latest('id')
                    ->take(4)
                    ->with('user:id,username,look')
                    ->get(),
            ]);
        });

        Fortify::registerView(function (Request $request) {
            if (setting('disable_registration') === '1') {
                return to_route('welcome')->withErrors([
                    'register' => __('Registration is currently disabled.'),
                ]);
            }

            return view('auth.register', [
                'referral_code' => $request->route('referral_code'),
                'articles' => WebsiteArticle::latest('id')
                    ->take(4)
                    ->has('user')
                    ->with('user:id,username,look')
                    ->get(),
                'photos' => CameraWeb::latest('id')
                    ->take(2)
                    ->with('user:id,username,look')
                    ->get(),
            ]);
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.passwords.confirm');
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        $this->authenticate();
    }

    private function authenticate()
    {
        Fortify::authenticateThrough(function () {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,

                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorConfirmed::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}
