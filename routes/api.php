<?php

use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\PageBlocks\PageBlockApiController;
use App\Http\Controllers\Storefronts\StorefrontApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'create']);
    Route::post('/login', [AuthApiController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/signout', [AuthApiController::class, 'destroy']);
});

Route::prefix('/dashboard')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('storefronts', StorefrontApiController::class)->only(['index', 'store']);
    Route::apiResource('storefronts', StorefrontApiController::class)->only(['show', 'update', 'destroy'])->middleware('can:manage,storefront');
    Route::apiResource('storefronts.blocks', PageBlockApiController::class)->middleware('can:manage,storefront');
});
