<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\ClaimReferralReward;
use App\Actions\User\UpdateAccountSettings;
use App\Data\PublicUserData;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Http\Requests\PasswordSettingsFormRequest;
use App\Http\Resources\Api\V1\PublicUserResource;
use App\Services\PermissionsService;
use App\Services\User\SessionService;
use App\Support\AuthenticatedUser;
use App\Support\StorefrontMoney;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function show(Request $request, PlayerSettingsRepository $settings, PermissionsService $permissions): JsonResponse
    {
        $user = AuthenticatedUser::from($request);

        return response()->json(['data' => [
            ...((new PublicUserResource(PublicUserData::from($user)))->resolve()),
            'mail' => $user->mail,
            'balances' => ['credits' => $user->currency('credits'), 'duckets' => $user->currency('duckets'), 'diamonds' => $user->currency('diamonds'), 'points' => $user->currency('points')],
            'website_balance' => ['amount_minor' => $user->website_balance, 'currency' => StorefrontMoney::currencyCode()],
            'can_change_name' => $settings->canChangeName($user),
            'can_generate_logo' => $permissions->allows($user, 'generate_logo'),
            'referral_code' => $user->referral_code, 'referrals_needed' => $user->referralsNeeded(),
            'two_factor_enabled' => $user->two_factor_secret !== null && $user->two_factor_confirmed_at !== null,
            'online_friends' => PublicUserResource::collection($user->getOnlineFriends()->map(fn ($friend) => PublicUserData::from($friend))),
        ]]);
    }

    public function update(AccountSettingsFormRequest $request, UpdateAccountSettings $settings): Response
    {
        $settings->execute(AuthenticatedUser::from($request), $request->validated());

        return response()->noContent();
    }

    public function password(PasswordSettingsFormRequest $request): Response
    {
        AuthenticatedUser::from($request)->changePassword($request->validated('password'));

        return response()->noContent();
    }

    public function sessions(Request $request, SessionService $sessions): JsonResponse
    {
        return response()->json(['data' => $sessions->forUser(AuthenticatedUser::from($request), $request->hasSession() ? $request->session()->getId() : '')->map(fn ($session): array => [
            'agent' => $session->agent, 'ip_address' => $session->ipAddress, 'is_current_device' => $session->isCurrentDevice, 'last_active' => $session->lastActive->toIso8601String(),
        ])]);
    }

    public function claimReferral(Request $request, ClaimReferralReward $claim): Response
    {
        if (! $claim->execute(AuthenticatedUser::from($request), $request->ip() ?: 'unknown')) {
            throw ValidationException::withMessages(['referrals' => __('You do not have enough referrals to claim your reward')]);
        }

        return response()->noContent();
    }
}
