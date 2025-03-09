<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;

Route::get('/test', function () {
    return response()->json(['message' => 'Conexión exitosa!']);
});
Route::get('/reseñas/google', [ReviewController::class, 'getGoogleReviews']);
Route::get('/reseñas/facebook', [ReviewController::class, 'getFacebookReviews']);
Route::get('/test-apis', [ReviewController::class, 'testApis']);