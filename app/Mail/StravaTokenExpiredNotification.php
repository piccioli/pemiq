<?php

namespace App\Mail;

use App\Models\StravaAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StravaTokenExpiredNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly StravaAccount $account) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Token Strava scaduto — ricollegamento necessario',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.strava-token-expired-notification',
        );
    }
}
