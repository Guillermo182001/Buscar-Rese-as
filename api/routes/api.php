<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\CorsMiddleware;

Route::get('/test', function () {
    return response()->json(['message' => 'Conexión exitosa!']);
});
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/reviews/google', [ReviewController::class, 'getGoogleReviews']);
    Route::get('/reviews/facebook', [ReviewController::class, 'getFacebookReviews']);
    Route::get('/test-apis', [ReviewController::class, 'testApis']);
});