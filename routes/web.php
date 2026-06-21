<?php

declare(strict_types=1);

use App\Http\Controllers\Web\ApiDemoController;
use App\Http\Controllers\Web\HealthPageController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\MetricsPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');
Route::get('/api-demo', ApiDemoController::class)->name('api-demo');
Route::get('/health', HealthPageController::class)->name('health');
Route::get('/metrics', MetricsPageController::class)->name('metrics');
