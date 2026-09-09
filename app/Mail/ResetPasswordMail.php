<?php

namespace App\Mail;

use App\Support\FrontendUrls;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $token) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Password');
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.forgetPassword',
            with: [
                'resetUrl' => app(FrontendUrls::class)->route('reset.password.get', ['token' => $this->token]),
            ],
        );
    }
}
