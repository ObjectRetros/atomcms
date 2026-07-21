<?php

namespace App\Http\Controllers\Shop;

use App\Contracts\PaypalGateway;
use App\Exceptions\PaypalPaymentException;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaypalPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaypalWebhookController extends Controller
{
    public function __invoke(Request $request, PaypalGateway $gateway, PaypalPaymentService $payments): JsonResponse
    {
        $webhookId = config('habbo.paypal.webhook_id');

        if (! is_string($webhookId) || $webhookId === '') {
            Log::critical('PayPal webhook received without PAYPAL_WEBHOOK_ID configuration.');

            return response()->json(['message' => 'Webhook is not configured.'], 503);
        }

        try {
            $verified = $gateway->verifyWebhook($request, $webhookId);
        } catch (Throwable $exception) {
            Log::warning('PayPal webhook verification could not be completed.', [
                'exception_class' => $exception::class,
            ]);

            return response()->json(['message' => 'Verification unavailable.'], 503);
        }

        if (! $verified) {
            return response()->json(['message' => 'Invalid webhook signature.'], 400);
        }

        try {
            $payments->handleWebhook($request->json()->all());
        } catch (PaypalPaymentException $exception) {
            Log::warning('Verified PayPal webhook processing failed.', [
                'event_id' => $request->input('id'),
                'event_type' => $request->input('event_type'),
                'exception_class' => $exception::class,
            ]);

            return response()->json(['message' => 'Webhook processing failed.'], 503);
        }

        return response()->json([], 200);
    }
}
