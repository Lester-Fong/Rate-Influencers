<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AdminInfluencerController;

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
    });

Route::prefix('v1')
    ->name('public.')
    ->group(function () {
        Route::get('/influencers', [InfluencerController::class, 'index'])
            ->name('influencers.index');
        Route::get('/influencers/{slug}', [InfluencerController::class, 'show'])
            ->name('influencers.show');
    });

// Store Review by Slug
Route::post("/{slug}", [ReviewController::class, "store"]);
