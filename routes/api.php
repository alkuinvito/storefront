<?php

use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\Media\MediaApiController;
use App\Http\Controllers\PageBlocks\PageBlockApiController;
use App\Http\Controllers\Storefronts\StorefrontApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'create']);
    Route::post('/login', [AuthApiController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/signout', [AuthApiController::class, 'destroy']);
});

Route::prefix('/dashboard')->group(function () {
    Route::apiResource('storefronts', StorefrontApiController::class)->only(['index', 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('storefronts', StorefrontApiController::class)->only(['store']);
        Route::apiResource('storefronts', StorefrontApiController::class)->only(['update', 'destroy'])->middleware('can:manage,storefront');

        Route::apiResource('storefronts.blocks', PageBlockApiController::class)->middleware('can:manage,storefront');

        Route::apiResource('media', MediaApiController::class)->only(['index', 'store', 'destroy']);
    });
});
