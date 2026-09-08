<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Models\Articles\WebsiteArticle;
use App\Models\Miscellaneous\CameraWeb;
use App\Support\FrontendUrls;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
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
        RateLimiter::for('login', function (Request $request): Limit {
            $username = $request->input('username');
            $identity = is_string($username)
                ? 'username:' . Str::transliterate(Str::lower($username))
                : 'invalid-username';

            return Limit::perMinute(5)->by($identity . '|' . $request->ip());
        });
        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
        RateLimiter::for('two-factor-settings', fn (Request $request) => Limit::perMinute(6)->by(
            ($request->user()?->getAuthIdentifier() ?? 'guest') . '|' . $request->ip(),
        ));
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('auth.login', $this->authPageData(4, 4)));

        Fortify::registerView(function (Request $request) {
            if (setting('disable_registration') === '1') {
                return redirect(app(FrontendUrls::class)->route('welcome'))->withErrors(['register' => __('Registration is currently disabled.')]);
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
            'photos' => Emulator::supports(Feature::CameraPhotos)
                ? CameraWeb::latest('id')->take($photos)->with('user:id,username,look')->get()
                : new Collection,
        ];
    }

    private function authenticate(): void
    {
        Fortify::authenticateThrough(function () {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,

                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}
