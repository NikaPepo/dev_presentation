<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\MetricRepositoryInterface;

class MetricsService
{
    public function __construct(private readonly MetricRepositoryInterface $metrics) {}

    public function recordContact(?float $aiDurationMs = null): void
    {
        $this->metrics->increment('contacts.total', 1.0);

        if ($aiDurationMs !== null) {
            $this->metrics->gauge('contacts.ai_duration_ms', $aiDurationMs);
        }
    }

    public function recordAiCall(bool $success, float $durationMs): void
    {
        $this->metrics->increment(
            $success ? 'ai.success' : 'ai.failure',
            1.0,
        );
        $this->metrics->gauge('ai.duration_ms', $durationMs, ['outcome' => $success ? 'success' : 'failure']);
    }

    public function recordMailFailure(): void
    {
        $this->metrics->increment('mail.failures', 1.0);
    }

    /** @return array<string, array{count:int,sum:float,avg:float,min:float,max:float}> */
    public function summary(): array
    {
        $names = ['contacts.total', 'ai.success', 'ai.failure', 'ai.duration_ms', 'mail.failures'];
        $out = [];
        foreach ($names as $name) {
            $out[$name] = $this->metrics->summary($name, now()->subDay());
        }
        return $out;
    }
}