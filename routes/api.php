<?php


use App\Features\Article\Controllers\ArticleController;
use App\Features\Account\Controllers\AuthController;
use App\Features\Account\Controllers\ProfileController;
use App\Features\Reaction\Controllers\ReactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Account routes
    Route::prefix('accounts')->name('accounts.')->group(function () {
        // Authentication routes
        Route::prefix('auth')->name('auth.')->group(function () {
            // without auth
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::get('verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    
            // with auth
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('resend', [AuthController::class, 'resend'])->middleware('throttle:6,1')->name('verification.resend');
                Route::post('logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('show', [AuthController::class, 'show'])->name('show');
                Route::patch('update', [AuthController::class, 'update'])->name('update');
            });
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            // without auth
            Route::get('public/{username}', [ProfileController::class, 'showPublic'])->name('show.public');

            // with auth
            Route::middleware('auth:sanctum')->group(function () {
                Route::get('private', [ProfileController::class, 'show'])->name('show');
                Route::patch('private', [ProfileController::class, 'update'])->name('update');
            });
        });
    });

    // Article routes
    Route::prefix("articles")->name('articles.')->group(function () {
        // with auth
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('', [ArticleController::class, 'store'])->name('store');
            Route::get('mine', [ArticleController::class, 'showMine'])->name('show.mine');
            Route::get('{id}/preview', [ArticleController::class, 'preview'])->name('preview');
            Route::patch('{id}', [ArticleController::class, 'update'])->name('update');
            Route::delete('{id}', [ArticleController::class, 'destroy'])->name('destroy');
            Route::post('{id}/publish', [ArticleController::class, 'publish'])->name('publish');
            Route::post('{id}/unpublish', [ArticleController::class, 'unpublish'])->name('unpublish');
        });

        // without
        Route::get('{username}/{slug}', [ArticleController::class, 'show'])->name('show');
    });

    // Reaction routes
    Route::prefix("articles")->name('articles.')->group(function () {
        // with auth
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('{id}/reaction', [ReactionController::class, 'store'])->name('like');
            Route::delete('{id}/reaction', [ReactionController::class, 'destroy'])->name('unlike');
        });
    });
});
