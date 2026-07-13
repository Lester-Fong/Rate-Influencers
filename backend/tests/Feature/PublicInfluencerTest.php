<?php

namespace Tests\Feature;

use App\Models\Influencer;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInfluencerTest extends TestCase
{
    use RefreshDatabase;

    private function createInfluencer(array $overrides = []): Influencer
    {
        return Influencer::query()->create(array_merge([
            'name' => 'Example Creator',
            'slug' => 'example-creator',
            'bio' => 'Creates useful educational content.',
            'rating' => 4.5,
            'review_count' => 2,
            'profile_picture' => 'https://example.com/profile.jpg',
            'facebook_link' => 'https://facebook.com/example',
            'youtube_link' => 'https://youtube.com/@example',
            'tiktok_link' => 'https://tiktok.com/@example',
            'instagram_link' => 'https://instagram.com/example',
        ], $overrides));
    }

    public function test_public_list_uses_the_versioned_contract(): void
    {
        $this->createInfluencer();
        $this->createInfluencer([
            'name' => 'Second Creator',
            'slug' => 'second-creator',
            'rating' => 0,
            'review_count' => 0,
        ]);

        $this->getJson('/api/v1/influencers')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Example Creator')
            ->assertJsonPath('data.0.review_count', 2)
            ->assertJsonMissingPath('data.0.reviews');
    }

    public function test_public_list_can_search_by_name_or_slug(): void
    {
        $this->createInfluencer();
        $this->createInfluencer([
            'name' => 'Second Creator',
            'slug' => 'special-channel',
        ]);

        $this->getJson('/api/v1/influencers?search=Example')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'example-creator');

        $this->getJson('/api/v1/influencers?search=special-channel')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Second Creator');
    }

    public function test_search_input_is_validated(): void
    {
        $this->getJson('/api/v1/influencers?search='.str_repeat('a', 101))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['search']);
    }

    public function test_public_detail_returns_api_fields_and_only_approved_reviews(): void
    {
        $influencer = $this->createInfluencer();
        $influencer->reviews()->createMany([
            [
                'reviewer_name' => 'Visible Reviewer',
                'rating' => 5,
                'review' => 'Visible review.',
                'status' => Review::STATUS_APPROVED,
            ],
            [
                'reviewer_name' => 'Hidden Reviewer',
                'rating' => 1,
                'review' => 'Hidden review.',
                'status' => Review::STATUS_PENDING,
            ],
        ]);

        $this->getJson('/api/v1/influencers/example-creator')
            ->assertOk()
            ->assertJsonPath('data.name', 'Example Creator')
            ->assertJsonPath('data.bio', 'Creates useful educational content.')
            ->assertJsonPath('data.rating', 4.5)
            ->assertJsonPath('data.review_count', 2)
            ->assertJsonPath('data.facebook_link', 'https://facebook.com/example')
            ->assertJsonCount(1, 'data.reviews')
            ->assertJsonPath('data.reviews.0.reviewer_name', 'Visible Reviewer')
            ->assertJsonMissing(['reviewer_name' => 'Hidden Reviewer']);
    }

    public function test_missing_influencer_returns_not_found(): void
    {
        $this->getJson('/api/v1/influencers/missing-creator')
            ->assertNotFound()
            ->assertJsonPath('message', 'Influencer not found.');
    }
}
