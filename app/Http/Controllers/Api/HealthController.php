<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    /**
     * Service health check.
     *
     * Probes three components: database, cache, and the OpenAI ping.
     *
     * @response 200 {
     *   "status": "ok",
     *   "components": {"db": "ok", "cache": "ok", "ai": "ok"}
     * }
     * @response 200 {
     *   "status": "degraded",
     *   "components": {"db": "ok", "cache": "ok", "ai": "fail"}
     * }
     * @response 503 {
     *   "status": "fail",
     *   "components": {"db": "fail", "cache": "ok", "ai": "ok"}
     * }
     */
    public function __invoke(HealthService $health): JsonResponse
    {
        $result = $health->check();
        $http = $result['status'] === 'fail' ? 503 : 200;

        return response()->json($result, $http);
    }
}