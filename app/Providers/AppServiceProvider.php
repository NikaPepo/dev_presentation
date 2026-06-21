<?php

declare(strict_types=1);

namespace App\Providers;

use App\Handlers\OpenAIHandler;
use App\Repositories\Contracts\ContactRepositoryInterface;
use App\Repositories\Contracts\MetricRepositoryInterface;
use App\Repositories\ContactRepository;
use App\Repositories\MetricRepository;
use App\Services\AIService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** @var array<class-string>, class-string> */
    public array $bindings = [
        ContactRepositoryInterface::class => ContactRepository::class,
        MetricRepositoryInterface::class => MetricRepository::class,
    ];

    /** @var array<class-string, bool> */
    public array $singletons = [
        AIService::class => AIService::class,
        OpenAIHandler::class => OpenAIHandler::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerRateLimiters();
    }

    /**
     * Built-in Laravel RateLimiter, no custom service needed. The
     * 'contact' limiter is referenced from routes/api.php as
     * throttle:contact and returns 429 + Retry-After automatically.
     */
    private function registerRateLimiters(): void
    {
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute((int) config('services.rate_limit.per_minute', 5))
                ->by($request->ip());
        });
    }
}