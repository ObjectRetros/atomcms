<?php

use App\Models\User;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

test('processing a top-up creates a pending transaction and redirects to PayPal', function () {
    installHotel();

    $user = User::factory()->create();

    $this->mock(PayPalClient::class)
        ->shouldReceive('createOrder')
        ->once()
        ->andReturn([
            'id' => 'ORDER-1',
            'links' => [
                ['rel' => 'self', 'href' => 'https://api.paypal.example/orders/ORDER-1'],
                ['rel' => 'approve', 'href' => 'https://paypal.example/approve/ORDER-1'],
            ],
        ]);

    $this->actingAs($user)
        ->get(route('paypal.process-transaction', ['amount' => 10]))
        ->assertRedirect('https://paypal.example/approve/ORDER-1');

    expect($user->transactions()->where('transaction_id', 'ORDER-1')->exists())->toBeTrue();
});

test('a completed capture credits the balance exactly once', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create();
    $user->transactions()->create(['transaction_id' => 'ORDER-2', 'amount' => 0]);
    $startBalance = (float) $user->website_balance;

    // The capture may only ever be attempted once, however often the
    // return-url is (re)visited.
    $this->mock(PayPalClient::class)
        ->shouldReceive('capturePaymentOrder')
        ->once()
        ->andReturn([
            'status' => 'COMPLETED',
            'purchase_units' => [[
                'payments' => ['captures' => [[
                    'status' => 'COMPLETED',
                    'amount' => ['value' => '10', 'currency_code' => 'USD'],
                ]]],
            ]],
        ]);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-2']))
        ->assertSessionHas('success');

    expect((float) $user->refresh()->website_balance)->toBe($startBalance + 10.0);

    // Replaying the return-url must not capture or credit again.
    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-2']))
        ->assertSessionHas('success');

    expect((float) $user->refresh()->website_balance)->toBe($startBalance + 10.0);
});

test('a capture in the wrong currency is not credited', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create();
    $user->transactions()->create(['transaction_id' => 'ORDER-3', 'amount' => 0]);
    $startBalance = (float) $user->website_balance;

    $this->mock(PayPalClient::class)
        ->shouldReceive('capturePaymentOrder')
        ->once()
        ->andReturn([
            'status' => 'COMPLETED',
            'purchase_units' => [[
                'payments' => ['captures' => [[
                    'status' => 'COMPLETED',
                    'amount' => ['value' => '10', 'currency_code' => 'EUR'],
                ]]],
            ]],
        ]);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-3']))
        ->assertSessionHasErrors('message');

    expect((float) $user->refresh()->website_balance)->toBe($startBalance);
});

test('an unknown order token is rejected', function () {
    installHotel();

    $user = User::factory()->create();

    $this->mock(PayPalClient::class)->shouldNotReceive('capturePaymentOrder');

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'UNKNOWN']))
        ->assertSessionHasErrors('message');
});
