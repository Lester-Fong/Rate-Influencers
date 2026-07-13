<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Influencer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInfluencerTest extends TestCase
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
            'Referer' => 'http://localhost:3000/admin/influencer',
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

    /**
     * @return array<string, string>
     */
    private function influencerData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Example Creator',
            'slug' => 'example-creator',
            'bio' => 'Creates educational videos.',
            'profile_picture' => 'https://example.com/profile.jpg',
            'facebook_link' => 'https://facebook.com/example',
            'youtube_link' => 'https://youtube.com/@example',
            'tiktok_link' => 'https://tiktok.com/@example',
            'instagram_link' => 'https://instagram.com/example',
        ], $overrides);
    }

    public function test_unauthenticated_users_cannot_access_admin_influencer_routes(): void
    {
        $influencer = Influencer::query()->create($this->influencerData());

        $this->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/admin/influencers')
            ->assertUnauthorized();
        $this->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/admin/influencers', $this->influencerData(['slug' => 'new-creator']))
            ->assertUnauthorized();
        $this->withHeaders($this->spaHeaders())
            ->patchJson("/api/v1/admin/influencers/{$influencer->id}", $this->influencerData())
            ->assertUnauthorized();
        $this->withHeaders($this->spaHeaders())
            ->deleteJson("/api/v1/admin/influencers/{$influencer->id}")
            ->assertUnauthorized();
    }

    public function test_administrator_can_list_and_create_influencers(): void
    {
        $administrator = $this->administrator();

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/admin/influencers', [
                ...$this->influencerData(),
                'rating' => 5,
                'review_count' => 999,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Example Creator')
            ->assertJsonPath('data.slug', 'example-creator')
            ->assertJsonPath('data.rating', 0)
            ->assertJsonPath('data.review_count', 0);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->getJson('/api/v1/admin/influencers')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'example-creator');
    }

    public function test_create_and_update_validation_are_enforced(): void
    {
        $administrator = $this->administrator();
        $influencer = Influencer::query()->create($this->influencerData());
        Influencer::query()->create($this->influencerData([
            'name' => 'Another Creator',
            'slug' => 'another-creator',
        ]));

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->postJson('/api/v1/admin/influencers', [
                'name' => '',
                'slug' => 'Invalid Slug',
                'profile_picture' => 'not-a-url',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'slug', 'profile_picture']);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->patchJson("/api/v1/admin/influencers/{$influencer->id}", $this->influencerData([
                'slug' => 'another-creator',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->patchJson("/api/v1/admin/influencers/{$influencer->id}", $this->influencerData())
            ->assertOk();
    }

    public function test_administrator_can_update_and_delete_an_influencer(): void
    {
        $administrator = $this->administrator();
        $influencer = Influencer::query()->create($this->influencerData());

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->patchJson("/api/v1/admin/influencers/{$influencer->id}", $this->influencerData([
                'name' => 'Updated Creator',
                'slug' => 'updated-creator',
            ]))
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Creator')
            ->assertJsonPath('data.slug', 'updated-creator');

        $this->assertDatabaseHas('influencers', [
            'id' => $influencer->id,
            'slug' => 'updated-creator',
        ]);

        $this->actingAs($administrator, 'web')
            ->withHeaders($this->spaHeaders())
            ->deleteJson("/api/v1/admin/influencers/{$influencer->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('influencers', ['id' => $influencer->id]);
    }
}
