<?php

namespace App\Http\Controllers\Badge;

use App\Http\Controllers\Controller;
use App\Actions\SendCurrency;
use App\Enums\CurrencyTypes;
use App\Models\WebsiteDrawBadge;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BadgeController extends Controller
{
    public function show(SettingsService $settingsService)
    {
        $cost = (int) $settingsService->getOrDefault('drawbadge_currency_value', 150);
        $currencyType = $settingsService->getOrDefault('drawbadge_currency_type', 'credits');
        $badgesPath = $settingsService->getOrDefault('badge_path_filesystem');

        $folderError = false;
        $errorMessage = '';

        if (!$badgesPath) {
            $folderError = true;
            $errorMessage = 'Badges path not configured.';
        } elseif (!file_exists($badgesPath)) {
            $folderError = true;
            $errorMessage = 'Badges path not configured.';
        } elseif (!is_writable($badgesPath)) {
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
        if (!$badgeData) {
            return response()->json(['success' => false, 'message' => 'No badge data provided.'], 400);
        }

        // Remove the data URL prefix if present
        $badgeData = preg_replace('#^data:image/\w+;base64,#i', '', $badgeData);

        // Decode the base64 data
        $decoded = base64_decode($badgeData, true);
        if ($decoded === false) {
            return response()->json(['success' => false, 'message' => 'Invalid base64 data.'], 400);
        }

        // Validate that it's a valid GIF image
        $info = @getimagesizefromstring($decoded);
        if ($info === false || $info['mime'] !== 'image/gif' || $info[0] !== 40 || $info[1] !== 40) {
            return response()->json(['success' => false, 'message' => 'Invalid GIF image or incorrect dimensions.'], 400);
        }

        // Optional: Additional size check (e.g., max 40KB for 40x40 GIF)
        if (strlen($decoded) > 40960) {
            return response()->json(['success' => false, 'message' => 'Image file too large.'], 400);
        }

        // To sanitize, load and re-encode the image using GD
        $image = @imagecreatefromstring($decoded);
        if ($image === false) {
            return response()->json(['success' => false, 'message' => 'Failed to process image.'], 400);
        }

        $badgesPath = $settingsService->getOrDefault('badge_path_filesystem');
        if (!$badgesPath) {
            return response()->json(['success' => false, 'message' => 'Badges path not configured.'], 500);
        }

        $filename = $user->id . '_' . time() . '.gif';

        $fullPath = rtrim($badgesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        // Save the re-encoded image
        if (!imagegif($image, $fullPath)) {
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