<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $message
 * @property string|null $ai_sentiment
 * @property string|null $ai_summary
 * @property float|null $ai_confidence
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'ai_sentiment',
        'ai_summary',
        'ai_confidence',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'ai_confidence' => 'float',
        ];
    }
}