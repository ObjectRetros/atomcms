<?php

use App\Mail\ResetPasswordMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('the full password reset flow works and tokens are single-use', function () {
    installHotel();
    Mail::fake();

    // The flow needs more requests than the stacked guest throttles allow.
    $this->withoutMiddleware(ThrottleRequests::class);

    $user = User::factory()->create();

    // Request a reset link.
    $this->post(route('forgot.password.post'), ['mail' => $user->mail])
        ->assertSessionHas('success');

    $token = null;
    Mail::assertQueued(ResetPasswordMail::class, function (ResetPasswordMail $mail) use (&$token) {
        $token = $mail->token;

        return true;
    });

    // The stored token is hashed - a database leak must not yield a usable link.
    expect(PasswordResetToken::whereKey($token)->exists())->toBeFalse()
        ->and(PasswordResetToken::whereKey(PasswordResetToken::hashToken($token))->exists())->toBeTrue();

    $this->get(route('reset.password.get', $token))->assertOk();

    $this->post(route('reset.password.post', $token), [
        'password' => 'N3wPassword!',
        'password_confirmation' => 'N3wPassword!',
    ])->assertRedirect(route('login'));

    expect(Hash::check('N3wPassword!', $user->fresh()->password))->toBeTrue();

    // The token is consumed and cannot be replayed.
    $this->post(route('reset.password.post', $token), [
        'password' => 'An0therPass!',
        'password_confirmation' => 'An0therPass!',
    ])->assertRedirect(route('forgot.password.get'));

    expect(Hash::check('N3wPassword!', $user->fresh()->password))->toBeTrue();
});

test('an expired token is rejected and deleted', function () {
    installHotel();

    $user = User::factory()->create();

    PasswordResetToken::create([
        'email' => $user->mail,
        'token' => PasswordResetToken::hashToken('expired-token'),
        'created_at' => now()->subMinutes((int) config('habbo.password_reset_token_time') + 1),
    ]);

    $this->get(route('reset.password.get', 'expired-token'))
        ->assertRedirect(route('forgot.password.get'));

    expect(PasswordResetToken::count())->toBe(0);
});

test('an unknown email gets the same response and no mail', function () {
    installHotel();
    Mail::fake();

    $this->post(route('forgot.password.post'), ['mail' => 'nobody@example.com'])
        ->assertSessionHas('success');

    Mail::assertNothingQueued();

    expect(PasswordResetToken::count())->toBe(0);
});
