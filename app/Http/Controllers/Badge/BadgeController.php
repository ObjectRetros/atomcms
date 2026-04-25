<?php

namespace App\Http\Controllers\Badge;

use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Badge\BadgePurchaseRequest;
use App\Models\User;
use App\Models\WebsiteDrawBadge;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BadgeController extends Controller
{
    private const BADGE_WIDTH = 40;

    private const BADGE_HEIGHT = 40;

    private const MAX_BADGE_SIZE_BYTES = 40960;

    public function show(SettingsService $settingsService)
    {
        $cost = (int) $settingsService->getOrDefault('drawbadge_currency_value', 150);
        $currencyType = $settingsService->getOrDefault('drawbadge_currency_type', 'credits');
        $badgesPath = $settingsService->getOrDefault('badge_path_filesystem');

        $folderError = false;
        $errorMessage = '';

        if (! $badgesPath) {
            $folderError = true;
            $errorMessage = 'Badges path not configured.';
        } elseif (! file_exists($badgesPath)) {
            $folderError = true;
            $errorMessage = 'Badges path not configured.';
        } elseif (! is_writable($badgesPath)) {
            $folderError = true;
            $errorMessage = 'Badges folder does not have write access.';
        }

        return view('draw-badge', compact('cost', 'currencyType', 'folderError', 'errorMessage'));
    }

    public function buy(BadgePurchaseRequest $request, SendCurrency $sendCurrency, SettingsService $settingsService)
    {
        $user = Auth::user();
        $cost = (int) $settingsService->getOrDefault('drawbadge_currency_value', 150);
        $currencyType = $settingsService->getOrDefault('drawbadge_currency_type', 'credits');

        $data = $request->validated();
        $badgeData = $data['badge_data'];

        $badgeData = preg_replace('#^data:image/\w+;base64,#i', '', $badgeData);
        $decoded = base64_decode($badgeData, true);

        if ($decoded === false) {
            return response()->json(['success' => false, 'message' => 'Invalid base64 data.'], 400);
        }

        $info = @getimagesizefromstring($decoded);
        if (
            $info === false ||
            $info['mime'] !== 'image/gif' ||
            $info[0] !== self::BADGE_WIDTH ||
            $info[1] !== self::BADGE_HEIGHT
        ) {
            return response()->json(['success' => false, 'message' => 'Invalid GIF image or incorrect dimensions.'], 400);
        }

        if (strlen($decoded) > self::MAX_BADGE_SIZE_BYTES) {
            return response()->json(['success' => false, 'message' => 'Image file too large.'], 400);
        }

        $image = @imagecreatefromstring($decoded);
        if ($image === false) {
            return response()->json(['success' => false, 'message' => 'Failed to process image.'], 400);
        }

        $badgesPath = $settingsService->getOrDefault('badge_path_filesystem');
        if (! $badgesPath) {
            return response()->json(['success' => false, 'message' => 'Badges path not configured.'], 500);
        }

        $filename = $user->id . '_' . time() . '.gif';
        $fullPath = rtrim($badgesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        if (! $this->chargeUser($user, $currencyType, $cost)) {
            imagedestroy($image);

            return response()->json(['success' => false, 'message' => 'Insufficient ' . $currencyType . '.'], 400);
        }

        if (! imagegif($image, $fullPath)) {
            $sendCurrency->execute($user, $currencyType, $cost);
            imagedestroy($image);

            return response()->json(['success' => false, 'message' => 'Failed to save badge file.'], 500);
        }

        imagedestroy($image);

        $baseUrl = $settingsService->getOrDefault('badges_path', '/badges/');
        $badgeUrl = rtrim($baseUrl, '/') . '/' . $filename;

        WebsiteDrawBadge::create([
            'user_id' => $user->id,
            'badge_path' => $fullPath,
            'badge_url' => $badgeUrl,
            'badge_name' => $data['badge_name'],
            'badge_desc' => $data['badge_description'],
        ]);

        return response()->json(['success' => true, 'badge_path_filesystem' => $fullPath]);
    }

    private function chargeUser(User $user, string $currencyType, int $amount): bool
    {
        return DB::transaction(function () use ($user, $currencyType, $amount) {
            if ($currencyType === 'credits') {
                return $user->newQuery()
                    ->whereKey($user->id)
                    ->where('credits', '>=', $amount)
                    ->decrement('credits', $amount) === 1;
            }

            $type = CurrencyTypes::fromCurrencyName($currencyType);

            if (! $type) {
                return false;
            }

            return $user->currencies()
                ->where('type', $type->value)
                ->where('amount', '>=', $amount)
                ->decrement('amount', $amount) === 1;
        });
    }
}
