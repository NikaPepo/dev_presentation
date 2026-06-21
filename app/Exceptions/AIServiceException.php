<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Thrown when the upstream AI provider (e.g. OpenAI) fails or returns an
 * unusable response. NOT surfaced to the HTTP client — ContactService catches
 * it and continues with graceful degradation (contact is still saved,
 * aiSummary stays null). Use report() to forward to the log.
 */
class AIServiceException extends RuntimeException
{
    public function __construct(
        string $message = 'AI service error',
        public readonly string $provider = 'openai',
        public readonly ?int $httpStatus = null,
        public readonly ?string $originalMessage = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}