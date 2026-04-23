<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnlineUserCountResource;
use App\Http\Resources\OnlineUsersResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelApiController extends Controller
{
    public function __construct(private readonly UserApiService $userApiService) {}

    public function fetchUser(string $username, array $columns = ['username', 'motto', 'look']): UserResource
    {
        return new UserResource($this->userApiService->fetchUser($username, $columns));
    }

    public function onlineUsers($columns = ['username', 'motto', 'look'], bool $randomOrder = true): OnlineUsersResource
    {
        return new OnlineUsersResource($this->userApiService->onlineUsers($columns, $randomOrder));
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->string('q')->trim()->value();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('username', 'like', $query . '%')
            ->select('username', 'look')
            ->limit(8)
            ->get();

        return response()->json($users);
    }

    public function onlineUserCount(): OnlineUserCountResource
    {
        return new OnlineUserCountResource($this->userApiService->onlineUserCount());
    }
}
