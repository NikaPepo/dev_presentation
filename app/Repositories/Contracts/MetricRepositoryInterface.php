<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Carbon\CarbonInterface;

interface MetricRepositoryInterface
{
    /** @param array<string, mixed> $tags */
    public function increment(string $name, float $delta = 1.0, array $tags = []): void;

    /** @param array<string, mixed> $tags */
    public function gauge(string $name, float $value, array $tags = []): void;

    /**
     * Aggregate (count, sum, avg, min, max) for a metric name since $since.
     * Cached for 60 seconds.
     *
     * @return array{count:int,sum:float,avg:float,min:float,max:float}
     */
    public function summary(string $name, ?CarbonInterface $since = null): array;
}