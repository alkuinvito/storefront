<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('/dashboard')->group(function () {
    Route::get('/', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
})->middleware(['auth', 'verified']);
