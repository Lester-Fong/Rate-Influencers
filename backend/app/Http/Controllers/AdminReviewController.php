<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewResourceCollection;
use App\Models\Review;
use App\Services\ReviewService;

class AdminReviewController extends Controller
{
    public function index(ReviewService $reviewService): ReviewResourceCollection
    {
        return new ReviewResourceCollection($reviewService->listAdminReviews());
    }

    public function approve(
        Review $review,
        ReviewService $reviewService
    ): ReviewResource {
        return new ReviewResource($reviewService->approveReview($review));
    }

    public function reject(
        Review $review,
        ReviewService $reviewService
    ): ReviewResource {
        return new ReviewResource($reviewService->rejectReview($review));
    }
}
