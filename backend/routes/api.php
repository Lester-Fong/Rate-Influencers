<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AdminInfluencerController;
use App\Http\Controllers\AdminReviewController;

Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AdministratorController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AdministratorController::class, 'me']);
        Route::post('/logout', [AdministratorController::class, 'logout']);
    });
});

Route::prefix('v1/admin')
    ->name('admin.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('influencers', AdminInfluencerController::class)
            ->except('show');

        Route::get('/reviews', [AdminReviewController::class, 'index'])
            ->name('reviews.index');
        Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])
            ->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])
            ->name('reviews.reject');
    });

Route::prefix('v1')
    ->name('public.')
    ->group(function () {
        Route::get('/influencers', [InfluencerController::class, 'index'])
            ->name('influencers.index');
        Route::get('/influencers/{slug}', [InfluencerController::class, 'show'])
            ->name('influencers.show');
        Route::get('/influencers/{influencer:slug}/reviews', [ReviewController::class, 'index'])
            ->name('influencers.reviews.index');
        Route::post('/influencers/{influencer:slug}/reviews', [ReviewController::class, 'store'])
            ->name('influencers.reviews.store');
    });
