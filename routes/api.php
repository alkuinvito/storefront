<?php

use App\Http\Controllers\Auth\AuthApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::get('/username/{username}', [AuthApiController::class, 'index']);
    Route::post('/login', [AuthApiController::class, 'store']);
    Route::post('/signout', [AuthApiController::class, 'destroy']);
    Route::middleware('auth:sanctum')->group(function () {});
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
