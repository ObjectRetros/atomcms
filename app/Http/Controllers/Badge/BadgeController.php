<?php

namespace App\Http\Controllers\Badge;

use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Http\Controllers\Controller;
use App\Models\WebsiteDrawBadge;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function buy(Request $request, SendCurrency $sendCurrency, SettingsService $settingsService)
    {
        $user = Auth::user();
        $cost = (int) $settingsService->getOrDefault('drawbadge_currency_value', 150);
        $currencyType = $settingsService->getOrDefault('drawbadge_currency_type', 'credits');

        $currentAmount = match ($currencyType) {
            'credits' => $user->credits ?? 0,
            'duckets' => $user->currencies()->where('type', CurrencyTypes::Duckets)->value('amount') ?? 0,
            'diamonds' => $user->currencies()->where('type', CurrencyTypes::Diamonds)->value('amount') ?? 0,
            'points' => $user->currencies()->where('type', CurrencyTypes::Points)->value('amount') ?? 0,
            default => 0,
        };

        if ($currentAmount < $cost) {
            return response()->json(['success' => false, 'message' => 'Insufficient ' . $currencyType . '.'], 400);
        }

        $result = $sendCurrency->execute($user, $currencyType, -$cost);

        if ($result === false) {
            return response()->json(['success' => false, 'message' => 'Failed to deduct ' . $currencyType . '.'], 500);
        }

        $badgeData = $request->input('badge_data');
        if (! $badgeData) {
            return response()->json(['success' => false, 'message' => 'No badge data provided.'], 400);
        }

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

        if (! imagegif($image, $fullPath)) {
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
            'badge_name' => $request->input('badge_name'),
            'badge_desc' => $request->input('badge_description'),
        ]);

        return response()->json(['success' => true, 'badge_path_filesystem' => $fullPath]);
    }
}
