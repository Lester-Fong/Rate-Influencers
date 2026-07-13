<?php

namespace App\Services;

use App\Models\Influencer;
use App\Models\Review;

class ReviewService
{
    /**
     * @param  array{reviewer_name: string, rating: int, review: string}  $data
     */
    public function submitReview(array $data, Influencer $influencer): Review
    {
        return $influencer->reviews()->create([
            ...$data,
            'status' => Review::STATUS_PENDING,
        ]);
    }
}
