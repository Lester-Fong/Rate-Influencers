<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Influencer;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('app.seed_admin');
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        $administrator = Administrator::query()->firstOrCreate(
            ['email' => $admin['email']],
            [
                'fullname' => $admin['fullname'],
                'password' => Hash::make($admin['password']),
            ]
        );

        if (! $administrator->fullname) {
            $administrator->update(['fullname' => $admin['fullname']]);
        }

        $this->call(InfluencerSeeder::class);

        $influencers = collect([
            [
                'name' => 'Addison Randall',
                'slug' => 'addison-randall',
                'bio' => 'Lifestyle creator sharing practical routines, creative projects, and candid behind-the-scenes videos.',
                'profile_picture' => "{$frontendUrl}/assets/avatar-addison.svg",
                'instagram_link' => 'https://www.instagram.com/',
                'youtube_link' => 'https://www.youtube.com/',
                'tiktok_link' => 'https://www.tiktok.com/',
                'facebook_link' => 'https://www.facebook.com/',
            ],
            [
                'name' => 'Hiram Sandoval',
                'slug' => 'hiram-sandoval',
                'bio' => 'Tech and productivity creator focused on approachable tutorials and honest gear discussions.',
                'profile_picture' => "{$frontendUrl}/assets/avatar-hiram.svg",
                'instagram_link' => 'https://www.instagram.com/',
                'youtube_link' => 'https://www.youtube.com/',
                'tiktok_link' => 'https://www.tiktok.com/',
                'facebook_link' => 'https://www.facebook.com/',
            ],
            [
                'name' => 'Maya Chen',
                'slug' => 'maya-chen',
                'bio' => 'Food storyteller exploring neighborhood restaurants, home recipes, and the people behind each dish.',
                'profile_picture' => "{$frontendUrl}/assets/avatar-maya.svg",
                'instagram_link' => 'https://www.instagram.com/',
                'youtube_link' => 'https://www.youtube.com/',
                'tiktok_link' => null,
                'facebook_link' => null,
            ],
        ])->mapWithKeys(function (array $data): array {
            $influencer = Influencer::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'rating' => 0, 'review_count' => 0]
            );

            return [$data['slug'] => $influencer];
        });

        $reviews = [
            ['addison-randall', 'Jordan Lee', 5, 'Clear, useful videos with a refreshingly honest point of view.', Review::STATUS_APPROVED],
            ['addison-randall', 'Sam Rivera', 4, 'Creative ideas and consistently thoughtful production.', Review::STATUS_APPROVED],
            ['hiram-sandoval', 'Taylor Brooks', 5, 'The tutorials are easy to follow and genuinely helpful.', Review::STATUS_PENDING],
            ['maya-chen', 'Avery Cruz', 4, 'Warm storytelling and excellent local recommendations.', Review::STATUS_APPROVED],
            ['maya-chen', 'Morgan Diaz', 2, 'This submission demonstrates rejected moderation state.', Review::STATUS_REJECTED],
        ];

        foreach ($reviews as [$slug, $reviewer, $rating, $body, $status]) {
            Review::query()->updateOrCreate(
                [
                    'influencer_id' => $influencers[$slug]->id,
                    'reviewer_name' => $reviewer,
                ],
                ['rating' => $rating, 'review' => $body, 'status' => $status]
            );
        }

        $influencers->each(function (Influencer $influencer): void {
            $approved = $influencer->reviews()->approved();
            $influencer->update([
                'rating' => (float) ($approved->avg('rating') ?? 0),
                'review_count' => $approved->count(),
            ]);
        });
    }
}
