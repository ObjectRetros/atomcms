<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeMessageRequest;
use App\Models\User;
use App\Services\Home\HomeService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class MessageController extends Controller
{
    public function store(User $user, HomeMessageRequest $request): JsonResponse
    {
        $authUser = AuthenticatedUser::from($request);

        try {
            app(HomeService::class)->postMessage($authUser, $user, $request->validated('content'));
        } catch (TooManyRequestsHttpException $exception) {
            return $this->jsonResponse(['message' => $exception->getMessage()], 429);
        }

        return $this->jsonResponse([
            'message' => __('Your message has been posted.'),
            'href' => route('home.show', $user->username),
        ]);
    }
}
