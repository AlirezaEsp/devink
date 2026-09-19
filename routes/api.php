<?php


use Illuminate\Support\Facades\Route;

use App\Features\Auth\Controllers\AuthController;
use App\Features\Account\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    // Authentication routes: register, login, logout
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
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
