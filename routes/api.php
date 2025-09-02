<?php

use App\Http\Controllers\Auth\LoginApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('/login', [LoginApiController::class, 'store']);
    Route::post('/signout', [LoginApiController::class, 'destroy']);
    Route::middleware('auth:sanctum')->group(function () {});
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
