<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\BuyHomeItemRequest;
use App\Models\Home\HomeItem;
use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function store(BuyHomeItemRequest $request): JsonResponse
    {
        $data = $request->validated();
        $item = HomeItem::with('homeCategory')->findOrFail($data['item_id']);
        $user = Auth::user();
        $totalPrice = $item->price * $data['quantity'];

        try {
            $this->homeService->verifyPurchasePossibility($user, $item, $data, $totalPrice);
            $this->homeService->buyItem($item, $user, $data, $totalPrice);
        } catch (\Throwable $exception) {
            return $this->jsonResponse([
                'message' => $exception->getMessage(),
            ], 400);
        }

        return $this->jsonResponse([
            'message' => __('You have successfully bought :quantity items.', ['quantity' => $data['quantity']]),
            'items' => $this->homeService->getLatestPurchaseItemIds($user, $item, $data['quantity']),
        ]);
    }

    public function getWidgetContent(string $username, int $itemId): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $item = $user->homeItems()->defaultRelationships()->find($itemId);

        if (! $item) {
            return $this->jsonResponse([
                'message' => __('Home item not found.'),
            ], 404);
        }

        $item->setWidgetContent($user);

        return $this->jsonResponse([
            'name' => $item->homeItem->name,
            'widget_type' => $item->widget_type,
            'content' => $item->content,
        ]);
    }
}
