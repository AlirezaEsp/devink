<?php


use Illuminate\Support\Facades\Route;

use App\Features\Account\Controllers\AuthController;
use App\Features\Account\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    Route::prefix('accounts')->name('accounts.')->group(function () {
        // Authentication routes: register, login, logout
        Route::prefix('auth')->name('auth.')->group(function () {
            // without auth
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    
            // with auth
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('show', [AuthController::class, 'show'])->name('show');
                Route::patch('update', [AuthController::class, 'update'])->name('update');
            });
        });

        // Profile routes: profile
        Route::prefix('profile')->name('profile.')->group(function () {
            // Public profile routes: showpublic (without auth)
            Route::get('profile/{username}', [ProfileController::class, 'showPublic'])->name('show.public');

            // Private profile routes: show, update (with auth)
            Route::middleware('auth:sanctum')->group(function () {
                Route::get('profile', [ProfileController::class, 'show'])->name('show');
                Route::patch('profile', [ProfileController::class, 'update'])->name('update');
            });
        });
    });

});
