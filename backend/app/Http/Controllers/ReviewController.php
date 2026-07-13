<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ReviewResourceCollection;
use App\Models\Influencer;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    public function index(
        Influencer $influencer,
        ReviewService $reviewService
    ): ReviewResourceCollection {
        return new ReviewResourceCollection(
            $reviewService->listApprovedReviews($influencer)
        );
    }

    public function store(
        ReviewRequest $request,
        Influencer $influencer,
        ReviewService $reviewService
    ) {
        $review = $reviewService->submitReview($request->validated(), $influencer);

        return (new ReviewResource($review))
            ->additional(['message' => 'Review has been submitted for approval.'])
            ->response()
            ->setStatusCode(201);
    }
}
