<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\ApiRateLimiter;

Route::get('/test', function () {
    return response()->json(['message' => 'Conexión exitosa!']);
});

// Aplicar middleware de CORS y Rate Limiting a todas las rutas de la API
Route::middleware([
    \App\Http\Middleware\CorsMiddleware::class,
    \App\Http\Middleware\ApiRateLimiter::class
])->group(function () {
    Route::get('/reviews/google', [ReviewController::class, 'getGoogleReviews']);
    Route::get('/reviews/facebook', [ReviewController::class, 'getFacebookReviews']);
    Route::get('/test-apis', [ReviewController::class, 'testApis']);
});