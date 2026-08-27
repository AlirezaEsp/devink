<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Features\Account\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    // Authentication routes: register, login, logout
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'store'])->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
