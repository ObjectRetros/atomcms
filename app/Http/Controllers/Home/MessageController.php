<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeMessageRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(User $user, HomeMessageRequest $request): JsonResponse
    {
        $authUser = Auth::user();

        if ($authUser->sentHomeMessages()->where('created_at', '>', now()->subMinute())->exists()) {
            return $this->jsonResponse([
                'message' => __('You are sending messages too fast.'),
            ], 429);
        }

        $user->receivedHomeMessages()->create([
            'user_id' => $authUser->id,
            'content' => strip_tags($request->validated('content')),
        ]);

        return $this->jsonResponse([
            'message' => __('Your message has been posted.'),
            'href' => route('home.show', $user->username),
        ]);
    }
}
