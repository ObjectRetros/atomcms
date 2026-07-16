<?php

use App\Contracts\PaypalGateway;
use App\Models\Shop\WebsitePaypalTransaction;
use App\Models\User;
use Illuminate\Http\Request;

function paypalTransaction(User $user, string $orderId, int $amount = 1000, string $currency = 'USD'): WebsitePaypalTransaction
{
    return $user->transactions()->create([
        'transaction_id' => $orderId,
        'status' => WebsitePaypalTransaction::STATUS_CREATED,
        'amount' => $amount,
        'currency' => $currency,
    ]);
}

/**
 * @return array<string, mixed>
 */
function completedPaypalOrder(
    string $orderId,
    string $captureId = 'CAPTURE-1',
    string $value = '10.00',
    string $currency = 'USD',
): array {
    return [
        'id' => $orderId,
        'status' => 'COMPLETED',
        'purchase_units' => [[
            'payments' => ['captures' => [[
                'id' => $captureId,
                'status' => 'COMPLETED',
                'amount' => ['value' => $value, 'currency_code' => $currency],
            ]]],
        ]],
    ];
}

test('processing a top-up stores expected minor units and redirects to PayPal', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create();
    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('createOrder')
        ->once()
        ->with(Mockery::on(fn (array $order): bool => data_get($order, 'purchase_units.0.amount') === [
            'currency_code' => 'USD',
            'value' => '10.00',
        ]))
        ->andReturn([
            'id' => 'ORDER-1',
            'links' => [
                ['rel' => 'self', 'href' => 'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER-1'],
                ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=ORDER-1'],
            ],
        ]);
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->post(route('paypal.process-transaction'), ['amount' => 10])
        ->assertRedirect('https://www.sandbox.paypal.com/checkoutnow?token=ORDER-1');

    $transaction = $user->transactions()->where('transaction_id', 'ORDER-1')->firstOrFail();

    expect($transaction->amount)->toBe(1000)
        ->and($transaction->currency)->toBe('USD')
        ->and($transaction->status)->toBe(WebsitePaypalTransaction::STATUS_CREATED);
});

test('a completed capture credits exact minor units once', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 250]);
    paypalTransaction($user, 'ORDER-2');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->with('ORDER-2', 'atom-capture-ORDER-2')
        ->andReturn(completedPaypalOrder('ORDER-2'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-2']))
        ->assertSessionHas('success');

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-2']))
        ->assertSessionHas('success');

    $transaction = $user->transactions()->where('transaction_id', 'ORDER-2')->firstOrFail();

    expect((int) $user->refresh()->website_balance)->toBe(1250)
        ->and($transaction->status)->toBe(WebsitePaypalTransaction::STATUS_COMPLETED)
        ->and($transaction->capture_id)->toBe('CAPTURE-1')
        ->and($transaction->credited_at)->not->toBeNull();
});

test('a capture with a different amount is held for review', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 250]);
    paypalTransaction($user, 'ORDER-3');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->andReturn(completedPaypalOrder('ORDER-3', value: '9.99'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-3']))
        ->assertSessionHasErrors('message');

    $transaction = $user->transactions()->where('transaction_id', 'ORDER-3')->firstOrFail();

    expect((int) $user->refresh()->website_balance)->toBe(250)
        ->and($transaction->status)->toBe(WebsitePaypalTransaction::STATUS_REVIEW)
        ->and($transaction->credited_at)->toBeNull();
});

test('a capture in a different currency is held for review', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 250]);
    paypalTransaction($user, 'ORDER-4');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->andReturn(completedPaypalOrder('ORDER-4', currency: 'EUR'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-4']))
        ->assertSessionHasErrors('message');

    expect((int) $user->refresh()->website_balance)->toBe(250)
        ->and($user->transactions()->where('transaction_id', 'ORDER-4')->value('status'))
        ->toBe(WebsitePaypalTransaction::STATUS_REVIEW);
});

test('an unknown return token never reaches PayPal', function () {
    installHotel();

    $user = User::factory()->create();
    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldNotReceive('captureOrder');
    $gateway->shouldNotReceive('showOrder');
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'UNKNOWN']))
        ->assertSessionHasErrors('message');
});

test('a verified completed-capture webhook credits an order without a return visit', function () {
    installHotel();
    config([
        'habbo.paypal.currency' => 'USD',
        'habbo.paypal.webhook_id' => 'WEBHOOK-1',
    ]);

    $user = User::factory()->create(['website_balance' => 0]);
    paypalTransaction($user, 'ORDER-5');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('verifyWebhook')
        ->once()
        ->with(Mockery::type(Request::class), 'WEBHOOK-1')
        ->andReturnTrue();
    $gateway->shouldNotReceive('captureOrder');
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->postJson(route('paypal.webhook'), [
        'id' => 'EVENT-1',
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAPTURE-5',
            'status' => 'COMPLETED',
            'amount' => ['value' => '10.00', 'currency_code' => 'USD'],
            'supplementary_data' => ['related_ids' => ['order_id' => 'ORDER-5']],
        ],
    ])->assertOk();

    expect((int) $user->refresh()->website_balance)->toBe(1000)
        ->and($user->transactions()->where('transaction_id', 'ORDER-5')->value('capture_id'))->toBe('CAPTURE-5');
});

test('an invalid webhook signature cannot alter a balance', function () {
    installHotel();
    config(['habbo.paypal.webhook_id' => 'WEBHOOK-1']);

    $user = User::factory()->create(['website_balance' => 0]);
    paypalTransaction($user, 'ORDER-6');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('verifyWebhook')->once()->andReturnFalse();
    $gateway->shouldNotReceive('captureOrder');
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->postJson(route('paypal.webhook'), [
        'id' => 'EVENT-2',
        'event_type' => 'CHECKOUT.ORDER.APPROVED',
        'resource' => ['id' => 'ORDER-6'],
    ])->assertStatus(400);

    expect((int) $user->refresh()->website_balance)->toBe(0);
});

test('reconciliation captures an approved order when the buyer never returns', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 0]);
    paypalTransaction($user, 'ORDER-7');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('showOrder')
        ->once()
        ->with('ORDER-7')
        ->andReturn(['id' => 'ORDER-7', 'status' => 'APPROVED']);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->with('ORDER-7', 'atom-capture-ORDER-7')
        ->andReturn(completedPaypalOrder('ORDER-7', 'CAPTURE-7'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->artisan('paypal:reconcile')->assertSuccessful();

    expect((int) $user->refresh()->website_balance)->toBe(1000)
        ->and($user->transactions()->where('transaction_id', 'ORDER-7')->value('capture_id'))->toBe('CAPTURE-7');
});

test('a pending order created before the money migration is reconciled safely', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 0]);
    paypalTransaction($user, 'ORDER-8', amount: 0)->update([
        'status' => WebsitePaypalTransaction::STATUS_LEGACY_CREATED,
    ]);

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('showOrder')
        ->once()
        ->with('ORDER-8')
        ->andReturn([
            'id' => 'ORDER-8',
            'status' => 'APPROVED',
            'purchase_units' => [[
                'amount' => ['value' => '10.00', 'currency_code' => 'USD'],
            ]],
        ]);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->with('ORDER-8', 'atom-capture-ORDER-8')
        ->andReturn(completedPaypalOrder('ORDER-8', 'CAPTURE-8'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($user)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-8']))
        ->assertSessionHas('success');

    $transaction = $user->transactions()->where('transaction_id', 'ORDER-8')->firstOrFail();

    expect((int) $user->refresh()->website_balance)->toBe(1000)
        ->and($transaction->amount)->toBe(1000)
        ->and($transaction->capture_id)->toBe('CAPTURE-8');
});

test('one PayPal capture cannot credit two different orders', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $firstUser = User::factory()->create(['website_balance' => 0]);
    $secondUser = User::factory()->create(['website_balance' => 0]);
    paypalTransaction($firstUser, 'ORDER-9');
    paypalTransaction($secondUser, 'ORDER-10');

    $gateway = Mockery::mock(PaypalGateway::class);
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->with('ORDER-9', 'atom-capture-ORDER-9')
        ->andReturn(completedPaypalOrder('ORDER-9', 'CAPTURE-SHARED'));
    $gateway->shouldReceive('captureOrder')
        ->once()
        ->with('ORDER-10', 'atom-capture-ORDER-10')
        ->andReturn(completedPaypalOrder('ORDER-10', 'CAPTURE-SHARED'));
    $this->app->instance(PaypalGateway::class, $gateway);

    $this->actingAs($firstUser)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-9']))
        ->assertSessionHas('success');

    $this->actingAs($secondUser)
        ->get(route('paypal.successful-transaction', ['token' => 'ORDER-10']))
        ->assertSessionHasErrors('message');

    expect((int) $firstUser->refresh()->website_balance)->toBe(1000)
        ->and((int) $secondUser->refresh()->website_balance)->toBe(0)
        ->and($secondUser->transactions()->where('transaction_id', 'ORDER-10')->value('status'))
        ->toBe(WebsitePaypalTransaction::STATUS_REVIEW);
});
