<?php

namespace App\Http\Controllers\Home;

use App\Enums\HomeItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\SaveHomeRequest;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function show(User $user): View
    {
        return view('home.show', [
            'user' => $user,
            'isMe' => Auth::id() === $user->id,
        ]);
    }

    public function getPlacedItems(User $user): JsonResponse
    {
        $allPlacedItems = $user->placedHomeItems()
            ->defaultRelationships(true)
            ->get();

        $filterByType = fn (HomeItemType $type) => $allPlacedItems
            ->filter(fn (UserHomeItem $item): bool => $item->homeItem?->type === $type)
            ->values();

        $notes = $filterByType(HomeItemType::Note)
            ->each(fn (UserHomeItem $item) => $item->setParsedData());

        $widgets = $filterByType(HomeItemType::Widget)
            ->each(fn (UserHomeItem $item) => $item->setWidgetContent($user));

        return $this->jsonResponse([
            'activeBackground' => $filterByType(HomeItemType::Background)->first(),
            'items' => $widgets->concat($notes)->concat($filterByType(HomeItemType::Sticker)),
        ]);
    }

    public function save(User $user, SaveHomeRequest $request): JsonResponse
    {
        try {
            $this->homeService->saveItems($user, $request->validated());
        } catch (\Throwable) {
            return $this->jsonResponse([
                'message' => __('An error occurred while saving your home.'),
            ], 500);
        }

        return $this->jsonResponse([
            'message' => __('Home saved successfully.'),
        ]);
    }
}
