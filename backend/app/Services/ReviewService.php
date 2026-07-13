<?php

namespace App\Services;

use App\Models\Influencer;
use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function listApprovedReviews(Influencer $influencer): Collection
    {
        return $influencer->reviews()
            ->approved()
            ->latest()
            ->get();
    }

    public function listAdminReviews(): Collection
    {
        return Review::query()
            ->with('influencer')
            ->latest()
            ->get();
    }

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

    public function approveReview(Review $review): Review
    {
        return $this->moderateReview($review, Review::STATUS_APPROVED);
    }

    public function rejectReview(Review $review): Review
    {
        return $this->moderateReview($review, Review::STATUS_REJECTED);
    }

    private function moderateReview(Review $review, string $status): Review
    {
        return DB::transaction(function () use ($review, $status): Review {
            $influencer = Influencer::query()
                ->lockForUpdate()
                ->findOrFail($review->influencer_id);

            $review->update(['status' => $status]);
            $this->recalculateInfluencerRating($influencer);

            return $review->refresh()->load('influencer');
        });
    }

    private function recalculateInfluencerRating(Influencer $influencer): void
    {
        $approvedReviews = $influencer->reviews()->approved();

        $influencer->update([
            'rating' => (float) ($approvedReviews->avg('rating') ?? 0),
            'review_count' => $approvedReviews->count(),
        ]);
    }
}
