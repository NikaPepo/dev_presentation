<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MailSendingException;
use App\Mail\OwnerContactMail;
use App\Mail\UserContactMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailService
{
    /**
     * Send both the owner notification and the user auto-reply.
     * Returns a list of human-readable warnings (empty when everything is fine).
     *
     * @return string[]
     */
    public function sendOwnerAndUser(Contact $contact): array
    {
        $warnings = [];

        if (! $this->sendOwner($contact)) {
            $warnings[] = 'Email notifications could not be sent';
        }

        return $warnings;
    }

    private function sendOwner(Contact $contact): bool
    {
        $ownerAddress = (string) config('mail.owner_address');

        if ($ownerAddress === '') {
            Log::warning('mail.owner_address is empty — skipping owner notification', [
                'contact_id' => $contact->id,
            ]);
            return false;
        }

        try {
            Mail::to($ownerAddress)->send(new OwnerContactMail($contact));
            return true;
        } catch (Throwable $e) {
            report(new MailSendingException(
                message: 'Failed to send owner notification',
                mailer: config('mail.default', 'smtp'),
                previous: $e,
            ));
            return false;
        }
    }

    public function sendUserConfirmation(Contact $contact): bool
    {
        try {
            Mail::to($contact->email)->send(new UserContactMail($contact));
            return true;
        } catch (Throwable $e) {
            report(new MailSendingException(
                message: 'Failed to send user confirmation',
                mailer: config('mail.default', 'smtp'),
                previous: $e,
            ));
            return false;
        }
    }
}