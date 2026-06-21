<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\ContactRequest;

/**
 * Immutable carrier for incoming contact form data. Keeps controllers and
 * services decoupled from the HTTP layer.
 */
final readonly class ContactDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $message,
        public ?string $ip = null,
        public ?string $userAgent = null,
    ) {}

    public static function fromRequest(ContactRequest $request): self
    {
        return new self(
            name: trim($request->string('name')->toString()),
            email: trim($request->string('email')->toString()),
            phone: trim($request->string('phone')->toString()),
            message: trim($request->string('message')->toString()),
            ip: $request->ip(),
            userAgent: $request->userAgent(),
        );
    }
}