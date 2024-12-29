<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\GoogleRecaptchaRule;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Symfony\Component\HttpFoundation\Response;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class RedirectIfTwoFactorAuthenticatable
{
    /**
     * The guard implementation.
     *
     * @var StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, callable $next)
    {
        $user = $this->validateCredentials($request);

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if ($user?->two_factor_secret &&
                !is_null($user?->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                return $this->twoFactorChallengeResponse($request, $user);
            } else {
                return $next($request);
            }
        }

        if ($user?->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        return $next($request);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @return mixed
     */
    protected function validateCredentials(Request $request)
    {
        if (Fortify::$authenticateUsingCallback) {
            return tap(call_user_func(Fortify::$authenticateUsingCallback, $request), function ($user) use ($request) {
                if (!$user) {
                    $this->fireFailedEvent($request);

                    $this->throwFailedAuthenticationException($request);
                }
            });
        }

        $model = $this->guard->getProvider()->getModel();

        return tap($model::where(Fortify::username(), $request->{Fortify::username()})->first(), function ($user) use ($request) {
            // Update the users password to bcrypt, if they previously used md5
            if ($user && config('habbo.site.convert_passwords')) {
                $this->convertUserPassword($user, $request->input('password'));
            }

            if (!$user || !$this->guard->getProvider()->validateCredentials($user, ['password' => $request->password])) {
                $this->fireFailedEvent($request, $user);

                $this->throwFailedAuthenticationException($request);
            }

            $this->validate($request);

            $user = User::select('id', 'password', 'rank')
                ->where('username', '=', $request->input('username'))
                ->first();

            if (setting('maintenance_enabled') === '1' && setting('min_maintenance_login_rank') > $user->rank) {
                throw ValidationException::withMessages([
                    'username' => __('Only staff can login during maintenance!'),
                ]);
            }
        });
    }

    /**
     * Throw a failed authentication validation exception.
     *
     *
     * @throws ValidationException
     */
    protected function throwFailedAuthenticationException(Request $request): void
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('auth.failed')],
        ])->errorBag('login');
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     */
    protected function fireFailedEvent(Request $request, Authenticatable $user = null): void
    {
        event(new Failed(config('fortify.guard'), $user, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     *
     * @param mixed $user
     */
    protected function twoFactorChallengeResponse(Request $request, $user): Response
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return $request->wantsJson()
            ? response()->json(['two_factor' => true])
            : redirect()->route('two-factor.login');
    }

    private function convertUserPassword(User $user, string $password)
    {
        if ($user->password == md5($password)) {
            $user->update([
                'password' => Hash::make($password),
            ]);
        }
    }

    private function validate(Request $request): array
    {
        $rules = [];

        if (setting('google_recaptcha_enabled')) {
            $rules['g-recaptcha-response'] = ['sometimes', 'string', new GoogleRecaptchaRule()];
        }

        if (setting('cloudflare_turnstile_enabled')) {
            $rules['cf-turnstile-response'] = [app(Turnstile::class)];
        }

        $messages = [
            'g-recaptcha-response.required' => __('The Google reCAPTCHA must be completed'),
            'g-recaptcha-response.string' => __('The Google reCAPTCHA was submitted with an invalid type'),
            'cf-turnstile-response.required' => __('The Cloudflare Turnstile response is required'),
        ];

        return Validator::make($request->all(), $rules, $messages)->validate();
    }
}
