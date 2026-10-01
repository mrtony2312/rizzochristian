<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, subject: ?string, message: string}  $contact
     */
    public function __construct(public array $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Abbiamo ricevuto il tuo messaggio – Rizzo Christian',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-confirmation',
        );
    }
}
