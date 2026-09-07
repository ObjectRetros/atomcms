<?php

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('Dusk renders password reset mail through the configured parent theme', function () {
    installHotel();
    setSetting('theme', 'dusk');
    config(['theme.parent' => 'atom']);
    Mail::fake();

    $user = User::factory()->create();

    $this->post(route('forgot.password.post'), ['mail' => $user->mail])
        ->assertSessionHas('success');

    Mail::assertQueued(ResetPasswordMail::class, function (ResetPasswordMail $mail): bool {
        expect($mail->render())->toContain('/reset-password/' . $mail->token);

        return true;
    });
});
