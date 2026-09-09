<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeRatingRequest;
use App\Models\User;
use App\Services\Home\HomeService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function store(User $user, HomeRatingRequest $request): JsonResponse
    {
        app(HomeService::class)->rate(AuthenticatedUser::from($request), $user, $request->integer('rating'));

        return $this->jsonResponse([
            'message' => __('Rating submitted successfully.'),
            'href' => route('home.show', $user->username),
        ]);
    }
}
