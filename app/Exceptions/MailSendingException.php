<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Thrown when MailService fails to deliver a message. NOT surfaced to the
 * HTTP client — ContactService catches it and continues (contact is still
 * saved, the failure ends up in warnings[]). Use report() to forward to log.
 */
class MailSendingException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to send email',
        public readonly string $mailer = 'smtp',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}