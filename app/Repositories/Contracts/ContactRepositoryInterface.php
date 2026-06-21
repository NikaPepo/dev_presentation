<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\ContactDTO;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

interface ContactRepositoryInterface
{
    public function create(ContactDTO $dto): Contact;

    public function findById(int $id): ?Contact;

    /** @return Collection<int, Contact> */
    public function recent(int $limit = 20): Collection;
}