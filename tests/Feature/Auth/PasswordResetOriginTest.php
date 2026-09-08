<?php

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('password reset mail uses the configured origin despite an untrusted request host', function () {
    installHotel();
    setSetting('theme', 'atom');
    Mail::fake();
    config(['app.url' => 'https://hotel.example']);

    $user = User::factory()->create();

    $this->post('http://untrusted.example/forgot-password', ['mail' => $user->mail])
        ->assertSessionHas('success');

    Mail::assertQueued(ResetPasswordMail::class, function (ResetPasswordMail $mail): bool {
        $html = $mail->render();

        expect($html)->toContain('https://hotel.example/reset-password/' . $mail->token)
            ->not->toContain('untrusted.example');

        return true;
    });
});
