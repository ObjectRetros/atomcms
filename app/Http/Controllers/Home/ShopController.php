<?php

namespace App\Http\Controllers\Home;

use App\Enums\HomeItemType;
use App\Http\Controllers\Controller;
use App\Models\Home\HomeCategory;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use App\Services\Home\HomeService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function categories(): JsonResponse
    {
        return $this->jsonResponse([
            'categories' => app(HomeService::class)->categories()->values(),
        ]);
    }

    public function itemsByCategory(HomeCategory $category): JsonResponse
    {
        return $this->jsonResponse([
            'items' => app(HomeService::class)->catalogItems($category, HomeItemType::Sticker)->values(),
        ]);
    }

    public function itemsByType(string $type): JsonResponse
    {
        $typeMap = [
            'notes' => HomeItemType::Note,
            'widgets' => HomeItemType::Widget,
            'backgrounds' => HomeItemType::Background,
        ];

        $itemType = $typeMap[$type] ?? null;

        if (! $itemType) {
            return $this->jsonResponse([
                'message' => __('Invalid item type.'),
            ], 404);
        }

        return $this->jsonResponse([
            'items' => app(HomeService::class)->catalogItems(type: $itemType)->values(),
        ]);
    }

    public function balance(): JsonResponse
    {
        $user = AuthenticatedUser::current();

        return $this->jsonResponse([
            'balance' => [
                '-1' => $user->credits,
                '0' => $user->currency('duckets'),
                '5' => $user->currency('diamonds'),
                '101' => $user->currency('points'),
            ],
        ]);
    }

    public function inventory(User $user): JsonResponse
    {
        abort_unless($user->id === Auth::id(), 403);

        $allInventoryItems = app(HomeService::class)->inventory(AuthenticatedUser::current(), $user, true);

        $filterByType = fn (HomeItemType $type) => $allInventoryItems
            ->filter(fn (UserHomeItem $item): bool => $item->homeItem?->type === $type)
            ->values();

        return $this->jsonResponse([
            'inventory' => [
                'stickers' => $filterByType(HomeItemType::Sticker),
                'notes' => $filterByType(HomeItemType::Note),
                'widgets' => $filterByType(HomeItemType::Widget),
                'backgrounds' => $filterByType(HomeItemType::Background),
            ],
        ]);
    }
}
