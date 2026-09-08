<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicUserData;
use App\Emulator\Data\Feature;
use App\Emulator\Emulator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PublicUserResource;
use App\Models\Miscellaneous\WebsiteLanguage;
use App\Models\User;
use App\Services\Community\CameraService;
use App\Services\HousekeepingPermissionsService;
use App\Services\InstallationService;
use App\Services\User\UserApiService;
use App\Support\StorefrontMoney;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BootstrapController extends Controller
{
    public function status(InstallationService $installation): JsonResponse
    {
        return response()->json(['data' => ['installed' => $installation->isComplete(), 'maintenance' => setting('maintenance_enabled') === '1', 'mode' => config('atom.mode', 'full')]]);
    }

    public function __invoke(InstallationService $installation, UserApiService $users, Request $request): JsonResponse
    {
        $installed = $installation->isComplete();
        $actor = $request->user();
        $viewer = $actor instanceof User ? [
            'id' => $actor->id, 'username' => $actor->username,
            'two_factor_enabled' => $actor->hasEnabledTwoFactorAuthentication(),
            'requires_two_factor' => (bool) setting('force_staff_2fa') && $actor->rank >= (int) setting('min_staff_rank') && ! $actor->hasEnabledTwoFactorAuthentication(),
            'can_access_housekeeping' => app(HousekeepingPermissionsService::class)->allows($actor, 'can_access_housekeeping'),
        ] : null;

        return response()->json(['data' => [
            'discord_url' => setting('discord_invitation_link'),
            'viewer' => $viewer, 'housekeeping_url' => url('housekeeping'),
            'captcha' => ['recaptcha_enabled' => (bool) setting('google_recaptcha_enabled'), 'recaptcha_site_key' => config('habbo.site.recaptcha_site_key'), 'turnstile_enabled' => (bool) setting('cloudflare_turnstile_enabled'), 'turnstile_site_key' => config('turnstile.turnstile_site_key')],
            'hotel_name' => setting('hotel_name', config('app.name')), 'hotel_description' => setting('hotel_description', ''),
            'mode' => config('atom.mode', 'full'), 'installed' => $installed,
            'maintenance' => setting('maintenance_enabled') === '1',
            'online_count' => $installed ? $users->onlineUserCount() : 0,
            'latest_photos' => $installed ? app(CameraService::class)->latestPhotos()->map(fn ($photo): array => ['id' => $photo->id, 'url' => url($photo->url), 'author' => $photo->user ? new PublicUserResource(PublicUserData::from($photo->user)) : null]) : [],
            'emulator' => Emulator::driver(),
            'features' => array_values(array_map(fn (Feature $feature): string => $feature->value, array_filter([Feature::CameraPhotos, Feature::RareValues], fn (Feature $feature): bool => Emulator::supports($feature)))),
            'locale' => app()->getLocale(),
            'locales' => $installed ? WebsiteLanguage::query()->get(['language', 'country_code'])->map(fn (WebsiteLanguage $language): array => ['name' => $language->language, 'locale' => $language->country_code])->all() : [],
            'assets' => ['logo' => url(setting('cms_logo', '/assets/images/logo.png')), 'avatar' => setting('avatar_imager'), 'badge' => url(setting('badges_path', '/client/flash/c_images/album1584'))],
            'registration' => ['enabled' => ! (bool) setting('disable_registration'), 'requires_beta_code' => (bool) setting('requires_beta_code')],
            'reactions' => config('habbo.reactions'),
            'operations' => ['articles', 'accounts', 'directory', 'staff', 'teams', 'leaderboards', 'applications', 'shop', 'paypal', 'vouchers', 'referrals', 'support', 'homes', 'badges', 'logo', 'nitro'],
            'currency' => StorefrontMoney::currencyCode(),
        ]]);
    }
}
