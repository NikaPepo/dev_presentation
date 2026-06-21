<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Logs every API request to two sinks:
 *   1. storage/logs/api-requests.log  (file, via 'api-requests' Log channel)
 *   2. api_request_logs table         (DB, for analytics)
 *
 * Uses terminate() so the request itself isn't slowed down.
 */
class ApiRequestLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('api.start_time', microtime(true));
        $request->attributes->set('api.request_id', (string) Str::uuid());

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $start = (float) $request->attributes->get('api.start_time', microtime(true));
        $durationMs = (int) round((microtime(true) - $start) * 1000);
        $requestId = (string) $request->attributes->get('api.request_id', '');
        $occurredAt = now();

        $payload = [
            'method' => $request->getMethod(),
            'path' => '/'.ltrim($request->path(), '/'),
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'duration_ms' => $durationMs,
            'request_id' => $requestId,
            'metadata' => [
                'query' => $request->query(),
            ],
            'occurred_at' => $occurredAt->toIso8601String(),
        ];

        // File sink — never break the response if it fails
        try {
            Log::channel('api-requests')->info('api.request', $payload);
        } catch (Throwable) {
            // swallow
        }

        // DB sink — also best-effort
        try {
            ApiRequestLog::query()->create($payload);
        } catch (Throwable) {
            // swallow — don't break the response over logging
        }
    }
}