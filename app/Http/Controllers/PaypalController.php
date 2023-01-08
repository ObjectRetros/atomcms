<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WebsiteShopTransaction;
use App\Services\PaypalService;
use App\Services\RconService;
use Illuminate\Http\Request;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;

class PaypalController extends Controller
{
    private OrdersCreateRequest $paypalRequest;

    public function __construct()
    {
        $this->paypalRequest = new OrdersCreateRequest();
    }

    public function create(Request $request, PaypalService $paypalService)
    {
        if (!$request->has('value') || !$request->has('api_token')) {
            return redirect()->back()->withErrors([
                'message' => __('An unknown error occurred. Please contact the owners in-game or on our Discord server for help.')
            ]);
        }

        (int)$topUpAmount = $request->input('value');
        if ($topUpAmount < 1) {
            return redirect()->back()->withErrors([
                'message' => __('The minimum top up value is 1$'),
            ]);
        }

        // Fetch the user purchasing
        $user = User::query()
            ->where('paypal_api_token', '=', $request->get('api_token'))
            ->first();

        if (is_null($user)) {
            return redirect()->back()->withErrors([
                'message' => __('It seems like we the user does not exist'),
            ]);
        }

        if ($request->user()->id !== $user->id) {
            return redirect()->back()->withErrors([
                'message' => __('You can only top up your own account'),
            ]);
        }

        $requestBody = [
            'intent' => 'CAPTURE',
            'application_context' => [
                'locale' => setting('paypal_locale'),
                'shipping_preference' => 'NO_SHIPPING',
                'brand_name' => setting('hotel_name'),
                'return_url' => route('shop.index'),
                'cancel_url' => route('shop.index'),
            ],
            'purchase_units' => [
                0 => [
                    'description' => sprintf('%s top up', setting('hotel_name')),
                    'amount' => [
                        'currency_code' => setting('paypal_currency'),
                        'value' => $topUpAmount,
                    ],
                ],
            ] ,
        ];

        $this->paypalRequest->headers["prefer"] = "return=representation";
        $this->paypalRequest->body = $requestBody;

        $client = $paypalService->client();

        try {
            $response = $client->execute($this->paypalRequest);

            // Save transaction in database
            $user->transactions()->create([
                'payment_id' => $response->result->id,
                'amount' => $topUpAmount,
            ]);

            // Return creation response to client
            return json_encode($response->result, JSON_PRETTY_PRINT);
        } catch (HttpException $e) {
            return $e;
        }
    }

    public function execute(Request $request, RconService $rcon, PaypalService $paypalService)
    {
        if (!$request->has('orderID')) {
            return 'No order ID';
        }

        $orderId = $request->get('orderID');
        $transaction = WebsiteShopTransaction::query()
            ->where('payment_id', '=', $orderId)
            ->first();

        if (is_null($transaction)) {
            return "Transaction not found";
        }

        $paypalCaptureRequest = new OrdersCaptureRequest($orderId);
        $client = $paypalService->client();

        try {
            $response = $client->execute($paypalCaptureRequest);

            $transaction->update([
                'status' => WebsiteShopTransaction::STATUS_COMPLETED,
            ]);

            if ($response->result->purchase_units[0]->payments->captures[0]->status != 'COMPLETED') {
                return "The transaction was not complete, please contact an owner";
            }

            $user = $transaction->user;
            if ($user->online) {
                $rcon->alertUser($user, __('Thank you for your purchase. We have booked your currency onto your account'));
            }

            $user->increment('website_store_balance', $transaction->amount);

            $transaction->update([
                'status' => WebsiteShopTransaction::STATUS_RECEIVED,
            ]);

            return json_encode($response->result, JSON_PRETTY_PRINT);
        } catch (HttpException $ex) {
            return to_route('shop.index')->withErrors([
                'message' => __('It seems like an error happened during your purchase. Please contact one of the owners, so that they can look into the issue.')
            ]);
        }
    }
}