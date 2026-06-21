<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OwnerContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Contact $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact-form submission: '.$this->contact->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.owner-contact',
            with: [
                'contact' => $this->contact,
            ],
        );
    }
}