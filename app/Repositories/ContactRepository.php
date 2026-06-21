<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\ContactDTO;
use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function create(ContactDTO $dto): Contact
    {
        return Contact::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'message' => $dto->message,
            'ip' => $dto->ip,
            'user_agent' => $dto->userAgent,
        ]);
    }

    public function findById(int $id): ?Contact
    {
        return Contact::query()->find($id);
    }

    public function recent(int $limit = 20): Collection
    {
        return Contact::query()->latest('id')->limit($limit)->get();
    }
}