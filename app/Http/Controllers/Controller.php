<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function jsonResponse(array $args, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => in_array($status, [200, 201]),
            ...$args,
        ], $status);
    }
}
