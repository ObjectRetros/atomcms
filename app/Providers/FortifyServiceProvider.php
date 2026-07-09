<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\DisableTwoFactorAuthentication;
use App\Actions\Fortify\RedirectIfTwoFactorConfirmed;
use App\Models\Articles\WebsiteArticle;
use App\Models\Miscellaneous\CameraWeb;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Collection;
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

        $this->configureRateLimiting();
        $this->configureViews();
        $this->authenticate();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->input('username') . $request->ip()));
        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('auth.login', $this->authPageData(4, 4)));

        Fortify::registerView(function (Request $request) {
            if (setting('disable_registration') === '1') {
                return to_route('welcome')->withErrors(['register' => __('Registration is currently disabled.')]);
            }

            return view('auth.register', [
                'referral_code' => $request->route('referral_code'),
                ...$this->authPageData(4, 2),
            ]);
        });

        Fortify::confirmPasswordView(fn () => view('auth.passwords.confirm'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
    }

    /**
     * Latest articles and camera photos shown alongside the auth forms.
     *
     * @return array{articles: Collection<int, WebsiteArticle>, photos: Collection<int, CameraWeb>}
     */
    private function authPageData(int $articles, int $photos): array
    {
        return [
            'articles' => WebsiteArticle::latest('id')->take($articles)->has('user')->with('user:id,username,look')->get(),
            'photos' => CameraWeb::latest('id')->take($photos)->with('user:id,username,look')->get(),
        ];
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
