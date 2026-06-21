<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\HealthService;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function __invoke(HealthService $health): View
    {
        return view('pages.landing', [
            'developer' => config('developer'),
            'health' => $health->check(),
        ]);
    }
}