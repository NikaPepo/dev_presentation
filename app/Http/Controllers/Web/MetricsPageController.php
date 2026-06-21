<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use App\Services\MetricsService;
use Illuminate\Contracts\View\View;

class MetricsPageController extends Controller
{
    public function __invoke(MetricsService $metrics): View
    {
        return view('pages.metrics', [
            'recent' => Metric::query()->latest('occurred_at')->limit(50)->get(),
            'summary' => $metrics->summary(),
        ]);
    }
}