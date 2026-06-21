<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Reserved for custom rate-limit handling. The base implementation uses
 * Laravel's built-in RateLimiter + throttle:contact middleware which returns
 * 429 automatically. This exception class is kept for future use (e.g. when
 * a service layer needs to programmatically enforce a per-user limit).
 */
class RateLimitException extends Exception
{
    public function __construct(
        string $message = 'Too many requests',
        public readonly int $limit = 0,
        public readonly int $retryAfter = 0,
    ) {
        parent::__construct($message, 429);
    }
}