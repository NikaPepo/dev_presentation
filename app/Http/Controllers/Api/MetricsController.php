<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Services\MetricsService;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    /**
     * Most recent raw metric events (newest first, capped at 200).
     *
     * @response 200 {
     *   "count": 3,
     *   "data": [
     *     {"id": 1, "name": "contacts.total", "type": "counter", "value": 1, "tags": {"category": "general"}, "occurred_at": "2026-06-21T12:00:00+00:00"}
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $rows = Metric::query()
            ->latest('occurred_at')
            ->limit(200)
            ->get(['id', 'name', 'type', 'value', 'tags', 'occurred_at']);

        return response()->json([
            'count' => $rows->count(),
            'data' => $rows,
        ]);
    }

    /**
     * Aggregated counters and gauges over the last 24 hours.
     *
     * @response 200 {
     *   "window": "24h",
     *   "metrics": {
     *     "contacts.total": {"count": 12, "sum": 12, "avg": 1, "min": 1, "max": 1},
     *     "ai.success":      {"count": 10, "sum": 10, "avg": 1, "min": 1, "max": 1}
     *   }
     * }
     */
    public function summary(MetricsService $metrics): JsonResponse
    {
        return response()->json([
            'window' => '24h',
            'metrics' => $metrics->summary(),
        ]);
    }
}