<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchUsersRequest;
use App\Http\Resources\OnlineUserCountResource;
use App\Http\Resources\UserResource;
use App\Services\User\UserApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HotelApiController extends Controller
{
    public function __construct(private readonly UserApiService $userApiService) {}

    public function fetchUser(string $username): UserResource|JsonResponse
    {
        $user = $this->userApiService->fetchUser($username);

        if ($user === null) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return new UserResource($user);
    }

    public function onlineUsers(): AnonymousResourceCollection
    {
        return UserResource::collection($this->userApiService->onlineUsers());
    }

    public function searchUsers(SearchUsersRequest $request): JsonResponse
    {
        $query = $request->string('q')->trim()->value();

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        return response()->json($this->userApiService->searchUsers($query));
    }

    public function onlineUserCount(): OnlineUserCountResource
    {
        return new OnlineUserCountResource($this->userApiService->onlineUserCount());
    }
}
