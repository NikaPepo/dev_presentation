<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthService
{
    public function __construct(private readonly AIService $ai) {}

    /**
     * Probe the three core components. Returns a status map and an overall
     * verdict:
     *   - all ok                 → status "ok"
     *   - db fail                → status "fail"  (HTTP 503 from controller)
     *   - db ok but ai/cache off → status "degraded" (HTTP 200 — graceful fallback works)
     *
     * @return array{status:string,components:array<string,string>}
     */
    public function check(): array
    {
        $components = [
            'db' => $this->checkDb(),
            'cache' => $this->checkCache(),
            'ai' => $this->checkAi(),
        ];

        $status = match (true) {
            $components['db'] !== 'ok' => 'fail',
            in_array('fail', $components, true) => 'degraded',
            default => 'ok',
        };

        return [
            'status' => $status,
            'components' => $components,
        ];
    }

    private function checkDb(): string
    {
        try {
            DB::connection()->getPdo();
            return 'ok';
        } catch (Throwable) {
            return 'fail';
        }
    }

    private function checkCache(): string
    {
        try {
            $store = Cache::store()->getStore();
            if (! method_exists($store, 'get') || ! method_exists($store, 'put')) {
                return 'fail';
            }
            Cache::put('__health', 'ok', 5);
            return Cache::get('__health') === 'ok' ? 'ok' : 'fail';
        } catch (Throwable) {
            return 'fail';
        }
    }

    private function checkAi(): string
    {
        try {
            return $this->ai->ping() ? 'ok' : 'fail';
        } catch (Throwable) {
            return 'fail';
        }
    }
}