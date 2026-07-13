<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Influencer;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DomainAlignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mvp_tables_and_columns_are_available(): void
    {
        $this->assertTrue(Schema::hasTable('reviews'));
        $this->assertFalse(Schema::hasTable('comments'));
        $this->assertTrue(Schema::hasColumns('reviews', [
            'reviewer_name',
            'rating',
            'review',
            'status',
            'influencer_id',
        ]));
        $this->assertTrue(Schema::hasColumns('influencers', ['bio', 'review_count']));
    }

    public function test_administrator_password_is_hashed_by_the_model(): void
    {
        $administrator = Administrator::query()->create([
            'fullname' => 'Arthur White',
            'email' => 'arthur.white@example.net',
            'password' => 'Test_123',
        ]);

        $this->assertTrue(Hash::check('Test_123', $administrator->password));
        $this->assertSame('Arthur White', $administrator->fullname);
    }

    public function test_public_influencer_response_only_contains_approved_reviews(): void
    {
        $influencer = Influencer::query()->create([
            'name' => 'Example Creator',
            'slug' => 'example-creator',
            'rating' => 5,
            'review_count' => 1,
        ]);

        $influencer->reviews()->createMany([
            [
                'reviewer_name' => 'Approved Reviewer',
                'rating' => 5,
                'review' => 'Visible review.',
                'status' => Review::STATUS_APPROVED,
            ],
            [
                'reviewer_name' => 'Pending Reviewer',
                'rating' => 1,
                'review' => 'Hidden review.',
                'status' => Review::STATUS_PENDING,
            ],
        ]);

        $this->getJson('/api/v1/influencers/example-creator')
            ->assertOk()
            ->assertJsonCount(1, 'data.reviews')
            ->assertJsonPath('data.reviews.0.reviewer_name', 'Approved Reviewer')
            ->assertJsonMissing(['reviewer_name' => 'Pending Reviewer']);
    }

    public function test_submitted_review_is_pending(): void
    {
        Influencer::query()->create([
            'name' => 'Example Creator',
            'slug' => 'example-creator',
        ]);

        $this->postJson('/api/example-creator', [
            'reviewer_name' => 'New Reviewer',
            'rating' => 4,
            'review' => 'A pending review.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', Review::STATUS_PENDING);

        $this->assertDatabaseHas('reviews', [
            'reviewer_name' => 'New Reviewer',
            'status' => Review::STATUS_PENDING,
        ]);
    }
}
