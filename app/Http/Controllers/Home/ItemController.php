<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\HomePurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\BuyHomeItemRequest;
use App\Models\User;
use App\Services\Home\HomeService;
use Illuminate\Http\JsonResponse;
use Throwable;

class ItemController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function store(User $user, BuyHomeItemRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $item = $this->homeService->buyItem($user, $data['item_id'], $data['quantity']);
        } catch (HomePurchaseException $exception) {
            return $this->jsonResponse([
                'message' => $exception->getMessage(),
            ], 400);
        } catch (Throwable $exception) {
            report($exception);

            return $this->jsonResponse([
                'message' => __('An error occurred while buying this item.'),
            ], 500);
        }

        return $this->jsonResponse([
            'message' => __('You have successfully bought :quantity items.', ['quantity' => $data['quantity']]),
            'items' => $this->homeService->getLatestPurchaseItemIds($user, $item, $data['quantity']),
        ]);
    }

    public function getWidgetContent(User $user, int $itemId): JsonResponse
    {
        $item = $user->homeItems()->defaultRelationships()->find($itemId);

        if ($item === null) {
            return $this->jsonResponse([
                'message' => __('Home item not found.'),
            ], 404);
        }

        $homeItem = $item->homeItem;

        if ($homeItem === null) {
            return $this->jsonResponse([
                'message' => __('Home item not found.'),
            ], 404);
        }

        $item->setWidgetContent($user);

        return $this->jsonResponse([
            'name' => $homeItem->name,
            'widget_type' => $item->widget_type,
            'content' => $item->content,
        ]);
    }
}
