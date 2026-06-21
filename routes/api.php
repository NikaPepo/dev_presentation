<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MetricsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.logger'])->group(function () {
    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:contact')
        ->name('api.contact.store');

    Route::post('/analyze', \App\Http\Controllers\Api\AnalyzeController::class)
        ->name('api.analyze');

    Route::get('/health', HealthController::class)->name('api.health');

    Route::get('/metrics', [MetricsController::class, 'index'])->name('api.metrics.index');
    Route::get('/metrics/summary', [MetricsController::class, 'summary'])->name('api.metrics.summary');
});