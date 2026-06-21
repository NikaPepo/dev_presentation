<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $method
 * @property string $path
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $user_agent
 * @property int|null $duration_ms
 * @property string|null $request_id
 * @property array<string, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $occurred_at
 */
class ApiRequestLog extends Model
{
    protected $table = 'api_request_logs';

    protected $fillable = [
        'method',
        'path',
        'status',
        'ip',
        'user_agent',
        'duration_ms',
        'request_id',
        'metadata',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'status' => 'integer',
            'duration_ms' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }
}