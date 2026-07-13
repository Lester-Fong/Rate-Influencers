<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdministratorController;

Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AdministratorController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AdministratorController::class, 'me']);
        Route::post('/logout', [AdministratorController::class, 'logout']);
    });
});

// Dislay all Influencers
Route::get("/", [InfluencerController::class, "index"]);
// Show Influencer by slug
Route::get("/{slug}", [InfluencerController::class, "show"]);
// Store Review by Slug
Route::post("/{slug}", [ReviewController::class, "store"]);
