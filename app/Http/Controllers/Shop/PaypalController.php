<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountTopupFormRequest;
use App\Http\Requests\PaypalTokenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Symfony\Component\HttpFoundation\Response;

class PaypalController extends Controller
{
    private const STATUS_CANCELLED = 'CANCELLED';

    private const STATUS_COMPLETED = 'COMPLETED';

    private PayPalClient $provider;

    public function process(AccountTopupFormRequest $request): Response|RedirectResponse
    {
        $provider = $this->provider();
        $amount = $request->integer('amount');
        $orderData = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.successful-transaction'),
                'cancel_url' => route('paypal.cancelled-transaction'),
                'brand_name' => setting('hotel_name'),
                'landing_page' => 'BILLING',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'CONTINUE',
            ],
            'purchase_units' => [
                0 => [
                    'amount' => [
                        'currency_code' => config('habbo.paypal.currency'),
                        'value' => (string) $amount,
                    ],
                ],
            ],
        ];

        $response = $provider->createOrder($orderData);

        if (isset($response['id']) === false) {
            Log::error('Error creating order', ['response' => $response]);

            return to_route('shop.index')->withErrors(
                ['message' => $response['message'] ?? __('Something went wrong')],
            );
        }

        foreach ($response['links'] as $links) {
            if ($links['rel'] === 'approve') {
                $request->user()->transactions()->create([
                    'transaction_id' => $response['id'],
                    'amount' => 0,
                ]);

                return redirect()->away($links['href']);
            }
        }

        return to_route('shop.index')->withErrors(
            ['message' => $response['message'] ?? __('Something went wrong')],
        );
    }

    public function successful(PaypalTokenRequest $request): Response
    {
        $user = $request->user();
        $provider = $this->provider();

        $transaction = $user->transactions()->where('transaction_id', $request['token'])->first();
        if ($transaction === null) {
            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please try again later')]);
        }

        $response = $provider->capturePaymentOrder($request['token']);
        $paymentDetails = $response['purchase_units'][0]['payments']['captures'][0] ?? null;

        if (! isset($response['status'], $paymentDetails)) {
            Log::error('Invalid response from PayPal', ['response' => $response]);

            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please try again later')]);
        }

        if ($paymentDetails === null) {
            $details = $response['error']['details'][0] ?? [
                'issue' => $response['name'] ?? 'PayPal error',
                'description' => $response['message'] ?? 'Unknown PayPal response',
            ];

            $transaction->update([
                'status' => $response['name'] ?? 'FAILED',
                'description' => sprintf('%s - %s', $details['issue'], $details['description']),
                'amount' => 0,
            ]);

            return to_route('shop.index')->withErrors(['message' => __('Something went wrong, please check your paypal account to make sure nothing was deducted and try again')]);
        }

        $transaction->update([
            'status' => $paymentDetails['status'],
            'amount' => $paymentDetails['amount']['value'],
            'currency' => $paymentDetails['amount']['currency_code'],
        ]);

        if ($response['status'] !== self::STATUS_COMPLETED) {
            return to_route('shop.index')->withErrors(
                ['message' => $response['message'] ?? __('Something went wrong')],
            );
        }

        $user->increment('website_balance', $paymentDetails['amount']['value']);

        return to_route('shop.index')->with('success', __('Transaction successful'));
    }

    public function cancelled(PaypalTokenRequest $request): Response
    {
        $transaction = $request->user()->transactions()->where('transaction_id', $request['token'])->first();
        if ($transaction !== null) {
            $transaction->update([
                'status' => self::STATUS_CANCELLED,
                'description' => 'The user cancelled the transaction',
            ]);
        }

        return to_route('shop.index')->withErrors(
            ['message' => __('You have canceled the transaction')],
        );
    }

    private function provider(): PayPalClient
    {
        if (isset($this->provider)) {
            return $this->provider;
        }

        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('habbo.paypal'));
        $this->provider->getAccessToken();

        return $this->provider;
    }
}
