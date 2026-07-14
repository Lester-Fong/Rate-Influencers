<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_mvp_seed_data_is_complete_and_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('administrators', 1);
        $this->assertDatabaseCount('influencers', 27);
        $this->assertDatabaseCount('reviews', 5);
        $this->assertDatabaseHas('administrators', [
            'email' => 'arthur.white@example.net',
            'fullname' => 'Arthur White',
        ]);
        $this->assertDatabaseHas('influencers', [
            'slug' => 'addison-randall',
            'rating' => 4.5,
            'review_count' => 2,
        ]);
        $this->assertDatabaseHas('influencers', [
            'slug' => 'hiram-sandoval',
            'rating' => 0,
            'review_count' => 0,
        ]);
        $this->assertDatabaseHas('influencers', [
            'slug' => 'bretman-rock',
            'name' => 'Bretman Rock',
            'instagram_link' => 'https://www.instagram.com/bretmanrock/',
        ]);
        $this->assertDatabaseHas('influencers', [
            'slug' => 'gordon-ramsay',
            'name' => 'Gordon Ramsay',
            'youtube_link' => 'https://www.youtube.com/@gordonramsay',
        ]);
    }
}
