<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdministratorController;

// Login Admin
Route::post("/login", [AdministratorController::class, "login"]);

// Dislay all Influencers
Route::get("/", [InfluencerController::class, "index"]);
// Show Influencer by slug
Route::get("/{slug}", [InfluencerController::class, "show"]);
// Store Comment by Slug
Route::post("/{slug}", [CommentController::class, "store"]);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');