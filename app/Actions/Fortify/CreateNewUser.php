<?php

namespace App\Actions\Fortify;

use App\Actions\Fortify\Rules\PasswordValidationRules;
use App\Jobs\SendRegisteredUserWebhook;
use App\Models\Miscellaneous\WebsiteBetaCode;
use App\Models\User;
use App\Rules\BetaCodeRule;
use App\Rules\GoogleRecaptchaRule;
use App\Rules\WebsiteWordfilterRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $ip = $this->registrationIp(request()->ip());
        $input = $this->validate($input);

        $user = $this->createUser($input, $ip);

        $this->applyBetaCode($input['beta_code'] ?? null, $user);
        $this->recordReferral($input['referral_code'] ?? null, $user, $ip);

        if (setting('enable_discord_webhook') === '1') {
            // After the response, so registration never waits on Discord.
            SendRegisteredUserWebhook::dispatchAfterResponse($user->username, $user->ip_register, $input['mail']);
        }

        return $user;
    }

    private function registrationIp(?string $ip): string
    {
        if ((setting('disable_registration') ?: '0') == '1') {
            throw ValidationException::withMessages(['registration' => __('Registration is disabled.')]);
        }

        if (! is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) === false) {
            throw ValidationException::withMessages(['registration' => __('Your IP address seems to be invalid')]);
        }

        $matchingIpCount = User::query()
            ->where('ip_current', $ip)
            ->orWhere('ip_register', $ip)
            ->count();

        if ($matchingIpCount >= (int) (setting('max_accounts_per_ip') ?: 99)) {
            throw ValidationException::withMessages(['registration' => __('You have reached the max amount of allowed account')]);
        }

        return $ip;
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function createUser(array $input, string $ip): User
    {
        $user = User::create([
            'username' => $input['username'],
            'mail' => $input['mail'],
            'password' => Hash::make($input['password']),
            'account_created' => time(),
            'last_login' => time(),
            'motto' => setting('start_motto') ?: 'Welcome to the hotel!',
            'look' => setting('start_look') ?: 'hr-100-61.hd-180-1.ch-210-66.lg-270-110.sh-305-62',
            'credits' => setting('start_credits') ?: 1000,
            'ip_register' => $ip,
            'ip_current' => $ip,
            'auth_ticket' => '',
            'home_room' => (int) (setting('hotel_home_room') ?: 0),
        ]);

        $user->update(['referral_code' => sprintf('%s%s', $user->id, Str::random(8))]);

        return $user;
    }

    private function applyBetaCode(?string $betaCode, User $user): void
    {
        if (! setting('requires_beta_code') || $betaCode === null) {
            return;
        }

        WebsiteBetaCode::where('code', $betaCode)->update(['user_id' => $user->id]);
    }

    /**
     * Award the referrer, unless the code is unknown or the two accounts share
     * an IP (referral farming). A bad code never aborts the registration.
     */
    private function recordReferral(?string $referralCode, User $user, string $ip): void
    {
        if ($referralCode === null) {
            return;
        }

        $referralUser = User::where('referral_code', $referralCode)->first();

        if ($referralUser === null
            || $referralUser->ip_current === $user->ip_current
            || $referralUser->ip_register === $user->ip_register) {
            return;
        }

        // Atomic increment so concurrent registrations cannot lose a count.
        $referralUser->referrals()
            ->firstOrCreate([], ['referrals_total' => 0])
            ->increment('referrals_total');

        $referralUser->userReferrals()->create([
            'referred_user_id' => $user->id,
            'referred_user_ip' => $ip,
        ]);
    }

    /**
     * @param  array<string, mixed>  $inputs
     *
     * @return array{username: string, mail: string, password: string, beta_code?: string, referral_code?: string}
     */
    private function validate(array $inputs): array
    {
        $rules = [
            'username' => ['required', 'string', sprintf('regex:%s', setting('username_regex') ?: '/^[a-zA-Z0-9_.-]+$/'), 'max:25', Rule::unique('users'), new WebsiteWordfilterRule],
            'mail' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => $this->passwordRules(),
            'beta_code' => ['sometimes', 'string', new BetaCodeRule],
            'referral_code' => ['sometimes', 'string', 'max:255'],
            'terms' => ['required', 'accepted'],
            'g-recaptcha-response' => ['sometimes', 'string', new GoogleRecaptchaRule],
            'cf-turnstile-response' => [app(Turnstile::class)],
        ];

        $messages = [
            'g-recaptcha-response.required' => __('The Google recaptcha must be completed'),
            'g-recaptcha-response.string' => __('The google recaptcha was submitted with an invalid type'),
        ];

        $validated = Validator::make($inputs, $rules, $messages)->validate();

        if (! is_string($validated['username'] ?? null)
            || ! is_string($validated['mail'] ?? null)
            || ! is_string($validated['password'] ?? null)) {
            throw new \UnexpectedValueException('Registration validation returned an invalid payload.');
        }

        $betaCode = $validated['beta_code'] ?? null;
        $referralCode = $validated['referral_code'] ?? null;

        if (($betaCode !== null && ! is_string($betaCode))
            || ($referralCode !== null && ! is_string($referralCode))) {
            throw new \UnexpectedValueException('Registration validation returned invalid optional values.');
        }

        $result = [
            'username' => $validated['username'],
            'mail' => $validated['mail'],
            'password' => $validated['password'],
        ];

        if ($betaCode !== null) {
            $result['beta_code'] = $betaCode;
        }

        if ($referralCode !== null) {
            $result['referral_code'] = $referralCode;
        }

        return $result;
    }
}
