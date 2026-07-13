<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Influencer;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, ReviewService $reviewService, string $slug)
    {
        $influencer = Influencer::query()->where('slug', $slug)->first();

        if (! $influencer) {
            return response()->json([
                'message' => 'This influencer is not yet recognized.',
            ], 404);
        }

        $review = $reviewService->submitReview($request->validated(), $influencer);

        return (new ReviewResource($review))
            ->additional(['message' => 'Review has been submitted for approval.'])
            ->response()
            ->setStatusCode(201);
    }
}
