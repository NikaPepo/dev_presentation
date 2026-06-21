<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Contact $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanks - we received your message',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-contact',
            with: [
                'contact' => $this->contact,
            ],
        );
    }
}
