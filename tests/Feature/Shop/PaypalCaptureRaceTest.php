<?php

use App\Enums\PaypalTransactionStatus;
use App\Models\User;
use App\Services\Payments\PaypalCaptureProcessor;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

test('a capture id claimed between the pre-check and the credit is held for review', function () {
    installHotel();
    config(['habbo.paypal.currency' => 'USD']);

    $user = User::factory()->create(['website_balance' => 0]);
    $victim = $user->transactions()->create([
        'transaction_id' => 'ORDER-RACE-1',
        'status' => PaypalTransactionStatus::Created,
        'amount' => 1000,
        'currency' => 'USD',
    ]);

    $other = User::factory()->create();
    $competitor = $other->transactions()->create([
        'transaction_id' => 'ORDER-RACE-2',
        'status' => PaypalTransactionStatus::Created,
        'amount' => 1000,
        'currency' => 'USD',
    ]);

    // Simulate a concurrent webhook: the moment the processor's exists()
    // pre-check has run (and seen nothing), assign the capture id to another
    // order so the processor's own update trips the unique index.
    $raced = false;
    DB::listen(function (QueryExecuted $query) use (&$raced, $competitor): void {
        if (! $raced && str_contains($query->sql, 'exists') && str_contains($query->sql, 'capture_id')) {
            $raced = true;

            DB::table('website_paypal_transactions')
                ->where('id', $competitor->id)
                ->update(['capture_id' => 'CAPTURE-RACED']);
        }
    });

    $credited = app(PaypalCaptureProcessor::class)->applyCompletedCapture('ORDER-RACE-1', [
        'id' => 'CAPTURE-RACED',
        'status' => 'COMPLETED',
        'amount' => ['value' => '10.00', 'currency_code' => 'USD'],
    ]);

    $victim->refresh();

    expect($raced)->toBeTrue()
        ->and($credited)->toBeFalse()
        ->and((int) $user->refresh()->website_balance)->toBe(0)
        ->and($victim->status)->toBe(PaypalTransactionStatus::Review)
        ->and($victim->capture_id)->toBeNull()
        ->and($victim->credited_at)->toBeNull()
        ->and($victim->description)->toBe('Capture ID was already assigned to another order.');
});
