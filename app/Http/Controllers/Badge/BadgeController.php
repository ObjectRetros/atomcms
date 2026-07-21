<?php

namespace App\Http\Controllers\Badge;

use App\Actions\Badge\CreateDrawnBadge;
use App\Http\Controllers\Controller;
use App\Http\Requests\Badge\BadgePurchaseRequest;
use App\Services\SettingsService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function show(SettingsService $settingsService): View
    {
        $error = $this->badgeFolderError($settingsService->getOrDefault('badge_path_filesystem'));

        return view('draw-badge', [
            'cost' => (int) $settingsService->getOrDefault('drawbadge_currency_value', 150),
            'currencyType' => $settingsService->getOrDefault('drawbadge_currency_type', 'credits'),
            'folderError' => $error !== null,
            'errorMessage' => $error ?? '',
        ]);
    }

    public function buy(BadgePurchaseRequest $request, CreateDrawnBadge $createDrawnBadge): JsonResponse
    {
        $validated = $request->validated();
        $badge = $createDrawnBadge->execute(AuthenticatedUser::from($request), [
            'badge_data' => (string) $validated['badge_data'],
            'badge_name' => (string) $validated['badge_name'],
            'badge_description' => (string) $validated['badge_description'],
        ]);

        return response()->json(['success' => true, 'badge_url' => $badge->badge_url]);
    }

    private function badgeFolderError(?string $path): ?string
    {
        return match (true) {
            ! $path => 'Badges path not configured.',
            ! file_exists($path) => 'Badges path not configured.',
            ! is_writable($path) => 'Badges folder does not have write access.',
            default => null,
        };
    }
}
