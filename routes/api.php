<?php

use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\Storefronts\StorefrontApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'create']);
    Route::post('/login', [AuthApiController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/signout', [AuthApiController::class, 'destroy']);
});

Route::prefix('/dashboard')->middleware('auth:sanctum')->group(function () {
    Route::prefix('/storefronts')->group(function () {
        Route::get('/', [StorefrontApiController::class, 'index']);
        Route::post('/', [StorefrontApiController::class, 'store']);
        Route::get('/{id}', [StorefrontApiController::class, 'show']);
        Route::put('/{id}', [StorefrontApiController::class, 'update']);
        Route::delete('/{id}', [StorefrontApiController::class, 'destroy']);
    });
});
