<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return $request->is('api/v1', 'api/v1/*') || parent::shouldReturnJson($request, $e);
    }

    public function render($request, Throwable $e): Response
    {
        if ($request->is('api/v1', 'api/v1/*')) {
            if ($e instanceof ShopPurchaseException || $e instanceof HomePurchaseException) {
                return response()->json(['code' => 'purchase_rejected', 'message' => $e->getMessage()], 422);
            }
            if ($e instanceof PaypalPaymentException || $e instanceof CurrencyGrantException || $e instanceof RconConnectionException) {
                return response()->json(['code' => 'integration_unavailable', 'message' => __('The integration could not complete this request.')], 503);
            }
        }

        $response = parent::render($request, $e);
        if (! $request->is('api/v1', 'api/v1/*') || ! $response instanceof JsonResponse) {
            return $response;
        }

        $status = $response->getStatusCode();
        $data = $response->getData(true);
        $code = match ($status) {
            400 => 'invalid_request',
            401 => 'unauthenticated',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            409 => 'conflict',
            419 => 'csrf_token_mismatch',
            422 => 'validation_failed',
            423 => 'password_confirmation_required',
            429 => 'rate_limited',
            503 => 'service_unavailable',
            default => 'server_error',
        };
        $response->setData([
            'code' => $e instanceof BadgePurchaseException ? 'badge_purchase_failed' : $code,
            'message' => $status >= 500 ? __('The request could not be completed.') : ($data['message'] ?? __('The request could not be completed.')),
            ...isset($data['errors']) ? ['errors' => $data['errors']] : [],
        ]);

        return $response;
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // The home widget endpoint is consumed by JavaScript that expects a
        // stable JSON error shape, regardless of the Accept header. Missing
        // route bindings and controller aborts both funnel through here.
        $this->renderable(function (NotFoundHttpException $e, Request $request): ?JsonResponse {
            if (! $request->routeIs('home.widget-content')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => __('Home item not found.'),
            ], 404);
        });
    }
}
