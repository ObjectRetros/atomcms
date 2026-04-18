<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeRatingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(string $username, HomeRatingRequest $request): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $user->homeRatings()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['rating' => $request->validated('rating')],
        );

        return $this->jsonResponse([
            'message' => __('Rating submitted successfully.'),
            'href' => route('home.show', $user->username),
        ]);
    }
}
