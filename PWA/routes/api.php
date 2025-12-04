<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PlaceVoteController;
use App\Http\Controllers\Api\AuthController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::apiResource('users', UserController::class);

// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');





Route::middleware('auth:sanctum')->group(function () {

    // Places
    Route::apiResource('places', PlaceController::class);

    // Photos
    Route::post('/photos', [PhotoController::class, 'store']);

    // Reviews
    //Route::post('/reviews', [ReviewController::class, 'store']);

    // Votes
    Route::post('/votes', [PlaceVoteController::class, 'store']);
    Route::delete('/votes/{place_id}', [PlaceVoteController::class, 'destroy']);
});


// Obtener fotos sin autenticación
Route::get('/photos/{id}', [PhotoController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('reviews', ReviewController::class)->except(['create', 'edit']);
});
