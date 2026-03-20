<?php

namespace App\Http\Controllers\Home;

use App\Enums\HomeItemType;
use App\Http\Controllers\Controller;
use App\Models\Home\HomeCategory;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    public function categories(): JsonResponse
    {
        return $this->jsonResponse([
            'categories' => HomeCategory::orderBy('order')->get()->values(),
        ]);
    }

    public function itemsByCategory(HomeCategory $category): JsonResponse
    {
        $category->load([
            'homeItems' => fn ($query) => $query->orderBy('order')->where('type', HomeItemType::Sticker),
        ]);

        return $this->jsonResponse([
            'items' => $category->homeItems->values(),
        ]);
    }

    public function itemsByType(string $type): JsonResponse
    {
        $typeChar = substr($type, 0, 1);
        $validTypes = HomeItemType::valuesExcept(HomeItemType::Sticker);

        if (! $typeChar || ! in_array($typeChar, $validTypes)) {
            return $this->jsonResponse([
                'message' => __('Invalid item type.'),
            ], 404);
        }

        return $this->jsonResponse([
            'items' => HomeItem::where('type', $typeChar)->orderBy('order')->get()->values(),
        ]);
    }

    public function inventory(string $username): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $allInventoryItems = $user->groupedInventoryItems()->get();

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
