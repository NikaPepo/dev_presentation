<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Metric;
use App\Repositories\Contracts\MetricRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MetricRepository implements MetricRepositoryInterface
{
    public function increment(string $name, float $delta = 1.0, array $tags = []): void
    {
        $this->record($name, $delta, 'counter', $tags);
    }

    public function gauge(string $name, float $value, array $tags = []): void
    {
        $this->record($name, $value, 'gauge', $tags);
    }

    public function summary(string $name, ?CarbonInterface $since = null): array
    {
        $since ??= now()->subDay();
        $cacheKey = "metrics:summary:{$name}:{$since->timestamp}";

        return Cache::remember($cacheKey, 60, function () use ($name, $since) {
            $row = Metric::query()
                ->where('name', $name)
                ->where('occurred_at', '>=', $since)
                ->selectRaw('COUNT(*) AS cnt, COALESCE(SUM(value),0) AS sum_v, COALESCE(AVG(value),0) AS avg_v, COALESCE(MIN(value),0) AS min_v, COALESCE(MAX(value),0) AS max_v')
                ->first();

            return [
                'count' => (int) ($row->cnt ?? 0),
                'sum' => (float) ($row->sum_v ?? 0.0),
                'avg' => (float) ($row->avg_v ?? 0.0),
                'min' => (float) ($row->min_v ?? 0.0),
                'max' => (float) ($row->max_v ?? 0.0),
            ];
        });
    }

    private function record(string $name, float $value, string $type, array $tags): void
    {
        Metric::query()->create([
            'name' => $name,
            'type' => $type,
            'value' => $value,
            'tags' => $tags,
            'occurred_at' => now(),
        ]);

        // bust summary cache when something new lands
        Cache::forget("metrics:summary:{$name}:".now()->subDay()->timestamp);
    }
}