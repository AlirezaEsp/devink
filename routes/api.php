<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Features\Auth\Controllers\AuthController;
use App\Features\Account\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    // Authentication routes: register, login, logout
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'store'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    });

    // Account routes: profile
    Route::prefix('account')->name('account.')->group(function () {
        // Profile routes: store, show, update
        Route::name('profile.')->middleware('api:sanctume')->group(function () {
            Route::post('profile', [ProfileController::class, 'store'])->name('store');
            Route::get('profile', [ProfileController::class, 'show'])->name('show');
            Route::patch('profile', [ProfileController::class, 'update'])->name('update');
        });
    });
});
