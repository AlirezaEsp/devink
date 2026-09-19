<?php


use Illuminate\Support\Facades\Route;

use App\Features\Auth\Controllers\AuthController;
use App\Features\Account\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    // Authentication routes: register, login, logout
    Route::prefix('auth')->name('auth.')->group(function () {
        // without auth
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');

        // with auth
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('show', [AuthController::class, 'show'])->name('show');
            Route::post('update', [AuthController::class, 'update'])->name('update');
        });
    });

    // Account routes: profile
    Route::prefix('account')->name('account.')->group(function () {
        // Private profile routes: show, update
        Route::name('profile.')->middleware('auth:sanctum')->group(function () {
            Route::get('profile', [ProfileController::class, 'show'])->name('show');
            Route::patch('profile', [ProfileController::class, 'update'])->name('update');
        });

        // Public profile routes: showpublic
        Route::name('profile.')->group(function () {
            Route::get('profile/{username}', [ProfileController::class, 'showPublic'])->name('show.public');
        });
    });
});
