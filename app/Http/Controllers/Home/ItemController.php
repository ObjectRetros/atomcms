<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\HomePurchaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\BuyHomeItemRequest;
use App\Models\Home\UserHomeItem;
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

    public function getWidgetContent(User $user, UserHomeItem $homeItem): JsonResponse
    {
        // The scoped route binding already 404s when the item does not belong
        // to the user; an orphaned item definition renders the same JSON 404
        // through the exception handler.
        $definition = $homeItem->load('homeItem:id,type,name,image')->homeItem;

        abort_if($definition === null, 404);

        $homeItem->setWidgetContent($user);

        return $this->jsonResponse([
            'name' => $definition->name,
            'widget_type' => $homeItem->widget_type,
            'content' => $homeItem->content,
        ]);
    }
}
