<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apis\AuthController;
use App\Http\Controllers\Apis\PropertyController;
use App\Http\Controllers\Apis\PropertyImageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

Route::middleware('auth:sanctum')->controller(PropertyController::class)->group(function () {
    Route::post('/properties', 'store');
    Route::put('/properties/{id}', 'update');
    Route::delete('/properties/{id}', 'destroy');
    Route::post('/properties/{id}/restore', 'restore');
});


Route::middleware('auth:sanctum')->controller(PropertyImageController::class)->group(function () {
    Route::post('/properties/{id}/images', 'store');
    Route::delete('/properties/{id}/images/{imageId}', 'destroy');
});
