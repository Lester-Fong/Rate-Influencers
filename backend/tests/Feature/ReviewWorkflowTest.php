<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Influencer;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function spaHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Origin' => 'http://localhost:3000',
            'Referer' => 'http://localhost:3000/admin/reviews',
        ];
    }

    private function administrator(): Administrator
    {
        return Administrator::query()->create([
            'fullname' => 'Arthur White',
            'email' => 'arthur.white@example.net',
            'password' => 'Test_123',
        ]);
    }

    private function influencer(): Influencer
    {
        return Influencer::query()->create([
            'name' => 'Example Creator',
            'slug' => 'example-creator',
            'rating' => 0,
            'review_count' => 0,
        ]);
    }

    public function test_public_review_routes_only_return_approved_reviews(): void
    {
        $influencer = $this->influencer();
        $influencer->reviews()->createMany([
            [
                'reviewer_name' => 'Approved Reviewer',
                'rating' => 5,
                'review' => 'Visible review.',
                'status' => Review::STATUS_APPROVED,
            ],
            [
                'reviewer_name' => 'Pending Reviewer',
                'rating' => 2,
                'review' => 'Hidden review.',
                'status' => Review::STATUS_PENDING,
            ],
            [
                'reviewer_name' => 'Rejected Reviewer',
                'rating' => 1,
                'review' => 'Also hidden.',
                'status' => Review::STATUS_REJECTED,
            ],
        ]);

        $this->getJson('/api/v1/influencers/example-creator/reviews')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reviewer_name', 'Approved Reviewer')
            ->assertJsonMissing(['reviewer_name' => 'Pending Reviewer'])
            ->assertJsonMissing(['reviewer_name' => 'Rejected Reviewer']);
    }

    public function test_public_submission_is_pending_and_validates_whole_star_ratings(): void
    {
        $this->influencer();

        $this->postJson('/api/v1/influencers/example-creator/reviews', [
            'reviewer_name' => 'New Reviewer',
            'rating' => 4,
            'review' => 'Awaiting moderation.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', Review::STATUS_PENDING)
            ->assertJsonPath('message', 'Review has been submitted for approval.');

        $this->postJson('/api/v1/influencers/example-creator/reviews', [
            'reviewer_name' => '',
            'rating' => 3.5,
            'review' => '',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['reviewer_name', 'rating', 'review']);

        $this->assertDatabaseHas('reviews', [
            'reviewer_name' => 'New Reviewer',
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_review_moderation_routes_require_authentication(): void
    {
        $review = $this->influencer()->reviews()->create([
            'reviewer_name' => 'Pending Reviewer',
            'rating' => 4,
            'review' => 'Pending review.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/admin/reviews')
            ->assertUnauthorized();
        $this->withHeaders($this->spaHeaders())
            ->postJson("/api/v1/admin/reviews/{$review->id}/approve")
            ->assertUnauthorized();
        $this->withHeaders($this->spaHeaders())
            ->postJson("/api/v1/admin/reviews/{$review->id}/reject")
            ->assertUnauthorized();
    }

    public function test_administrator_can_list_and_approve_reviews_with_recalculation(): void
    {
        $administrator = $this->administrator();
        $influencer = $this->influencer();
        $influencer->reviews()->create([
            'reviewer_name' => 'Existing Reviewer',
            'rating' => 5,
            'review' => 'Already approved.',
            'status' => Review::STATUS_APPROVED,
        ]);
        $pendingReview = $influencer->reviews()->create([
            'reviewer_name' => 'Pending Reviewer',
            'rating' => 3,
            'review' => 'Please approve.',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/admin/reviews')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.influencer.name', 'Example Creator');

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->postJson("/api/v1/admin/reviews/{$pendingReview->id}/approve")
            ->assertOk()
            ->assertJsonPath('data.status', Review::STATUS_APPROVED);

        $this->assertDatabaseHas('influencers', [
            'id' => $influencer->id,
            'rating' => 4,
            'review_count' => 2,
        ]);
    }

    public function test_rejecting_an_approved_review_recalculates_rating_and_count(): void
    {
        $administrator = $this->administrator();
        $influencer = $this->influencer();
        $reviewToReject = $influencer->reviews()->create([
            'reviewer_name' => 'Five Star Reviewer',
            'rating' => 5,
            'review' => 'Remove from aggregate.',
            'status' => Review::STATUS_APPROVED,
        ]);
        $influencer->reviews()->create([
            'reviewer_name' => 'Three Star Reviewer',
            'rating' => 3,
            'review' => 'Keep in aggregate.',
            'status' => Review::STATUS_APPROVED,
        ]);
        $influencer->update(['rating' => 4, 'review_count' => 2]);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->postJson("/api/v1/admin/reviews/{$reviewToReject->id}/reject")
            ->assertOk()
            ->assertJsonPath('data.status', Review::STATUS_REJECTED);

        $this->assertDatabaseHas('influencers', [
            'id' => $influencer->id,
            'rating' => 3,
            'review_count' => 1,
        ]);
    }
}
