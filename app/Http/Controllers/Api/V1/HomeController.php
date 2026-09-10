<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicUserData;
use App\Enums\HomeItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\BuyHomeItemRequest;
use App\Http\Requests\Home\HomeMessageRequest;
use App\Http\Requests\Home\HomeRatingRequest;
use App\Http\Requests\Home\SaveHomeRequest;
use App\Http\Resources\Api\V1\PublicUserResource;
use App\Models\Home\HomeItem;
use App\Models\Home\UserHomeItem;
use App\Models\User;
use App\Services\Home\HomeService;
use App\Support\AuthenticatedUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homes) {}

    public function show(User $user): JsonResponse
    {
        $items = $this->homes->placedItems($user);
        $background = $items->first(fn ($item): bool => $item->homeItem?->type === HomeItemType::Background);

        return response()->json(['data' => ['user' => new PublicUserResource(PublicUserData::from($user)), 'member_since' => CarbonImmutable::createFromTimestampUTC($user->account_created)->toIso8601String(), 'active_background' => $background ? $this->itemData($background) : null, 'items' => $items->filter(fn ($item): bool => $item->homeItem?->type !== HomeItemType::Background)->map(fn ($item): array => $this->itemData($item))->values()]]);
    }

    public function widget(User $user, UserHomeItem $homeItem, Request $request): JsonResponse
    {
        abort_unless($homeItem->user_id === $user->id && ($homeItem->placed || $request->user()?->is($user)), 404);
        $definition = $homeItem->load('homeItem')->homeItem;
        abort_unless($definition !== null && $definition->type === HomeItemType::Widget, 404);
        $type = $definition->getAvailableWidgets()[$definition->name] ?? null;
        abort_if($type === null, 404);
        $homeItem->widget_type = $type;
        $user = $this->homes->loadWidgetData($user, $homeItem, 'api.v1.homes.show');
        $data = match ($type) {
            'my-profile' => new PublicUserResource(PublicUserData::from($user)),
            'my-rooms' => $user->getRelation('rooms')->map(fn ($room): array => ['id' => $room->id, 'name' => $room->name, 'description' => $room->description, 'state' => $room->state]),
            'my-badges' => $this->page($user->getRelation('badges'), fn ($badge): array => ['code' => $badge->badge_code, 'slot' => $badge->slot]),
            'my-friends' => $this->page($user->getRelation('friends'), fn ($friend) => $friend->user ? new PublicUserResource(PublicUserData::from($friend->user)) : null),
            'my-rating' => ['average' => (float) $user->homeRatingStats?->rating_avg, 'total' => (int) $user->homeRatingStats?->total, 'positive' => (int) $user->homeRatingStats?->most_positive],
            'my-guestbook' => $user->receivedHomeMessages->map(fn ($message): array => ['id' => $message->id, 'content' => $message->content, 'created_at' => $message->created_at?->toIso8601String(), 'author' => $message->user ? new PublicUserResource(PublicUserData::from($message->user)) : null]),
            default => null,
        };

        return response()->json(['data' => ['id' => $homeItem->id, 'type' => $type, 'supported' => $type !== 'my-groups', 'content' => $data]]);
    }

    public function inventory(User $user, Request $request): JsonResponse
    {
        abort_unless($user->is(AuthenticatedUser::from($request)), 403);

        return response()->json(['data' => $this->homes->inventory(AuthenticatedUser::from($request), $user)->map(fn ($item): array => $this->itemData($item))]);
    }

    public function shop(): JsonResponse
    {
        return response()->json(['data' => [
            'categories' => $this->homes->categories()->map(fn ($category): array => ['id' => $category->id, 'name' => $category->name, 'icon' => filled($category->icon) ? url(str_starts_with($category->icon, '/') || str_starts_with($category->icon, 'http') ? $category->icon : 'storage/' . $category->icon) : null]),
            'items' => $this->homes->catalogItems()->map(fn ($item): array => $this->definitionData($item)),
        ]]);
    }

    public function save(User $user, SaveHomeRequest $request): Response
    {
        $this->homes->saveItems(AuthenticatedUser::from($request), $user, $request->validated());

        return response()->noContent();
    }

    public function purchase(User $user, BuyHomeItemRequest $request): JsonResponse
    {
        $item = $this->homes->buyItem(AuthenticatedUser::from($request), $request->integer('item_id'), $request->integer('quantity'));

        return response()->json(['data' => $this->definitionData($item)], 201);
    }

    public function message(User $user, HomeMessageRequest $request): Response
    {
        $this->homes->postMessage(AuthenticatedUser::from($request), $user, $request->validated('content'));

        return response()->noContent(201);
    }

    public function rating(User $user, HomeRatingRequest $request): Response
    {
        $this->homes->rate(AuthenticatedUser::from($request), $user, $request->integer('rating'));

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function itemData(UserHomeItem $item): array
    {
        return ['id' => $item->id, 'x' => $item->x, 'y' => $item->y, 'z' => $item->z, 'placed' => $item->placed, 'is_reversed' => $item->is_reversed, 'theme' => $item->theme, 'extra_data' => $item->extra_data, 'definition' => $item->homeItem ? $this->definitionData($item->homeItem) : null];
    }

    /** @return array<string, mixed> */
    private function definitionData(HomeItem $item): array
    {
        return ['id' => $item->id, 'category_id' => $item->home_category_id, 'type' => $item->type->value, 'name' => $item->name, 'image' => $item->image === '' ? null : url(str_starts_with($item->image, '/') || str_starts_with($item->image, 'http') ? $item->image : 'storage/' . $item->image), 'price' => $item->price, 'currency' => $item->currency_type->value, 'limit' => $item->limit, 'total_bought' => $item->total_bought, 'widget_type' => $item->type === HomeItemType::Widget ? ($item->getAvailableWidgets()[$item->name] ?? null) : null];
    }

    /**
     * @template T
     *
     * @param  LengthAwarePaginator<int, T>  $paginator
     * @param  callable(T): mixed  $map
     *
     * @return array<string, mixed>
     */
    private function page(LengthAwarePaginator $paginator, callable $map): array
    {
        return ['items' => $paginator->getCollection()->map($map), 'current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()];
    }
}
