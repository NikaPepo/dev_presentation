<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property float $value
 * @property array<string, mixed>|null $tags
 * @property \Illuminate\Support\Carbon $occurred_at
 */
class Metric extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'tags',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'tags' => 'array',
            'occurred_at' => 'datetime',
        ];
    }
}