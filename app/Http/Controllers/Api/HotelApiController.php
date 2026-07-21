<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnlineUserCountResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:25'],
        ]);

        $query = $request->string('q')->trim()->value();

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        // Escape LIKE wildcards so user input cannot widen the prefix match.
        $users = User::where('username', 'like', addcslashes($query, '\\%_') . '%')
            ->limit(8)
            ->get(['username', 'look']);

        return response()->json($users->map(fn (User $user): array => [
            'username' => $user->username,
            'look' => $user->look,
        ])->values());
    }

    public function onlineUserCount(): OnlineUserCountResource
    {
        return new OnlineUserCountResource($this->userApiService->onlineUserCount());
    }
}
