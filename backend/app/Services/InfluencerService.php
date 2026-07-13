<?php

namespace App\Services;

use App\Models\Influencer;
use Illuminate\Database\Eloquent\Collection;

class InfluencerService
{
    public function listAdminInfluencers(): Collection
    {
        return Influencer::query()
            ->orderBy('name')
            ->get();
    }

    public function createInfluencer(array $data): Influencer
    {
        return Influencer::query()->create([
            ...$data,
            'rating' => 0,
            'review_count' => 0,
        ]);
    }

    public function updateInfluencer(Influencer $influencer, array $data): Influencer
    {
        $influencer->update($data);

        return $influencer->refresh();
    }

    public function deleteInfluencer(Influencer $influencer): void
    {
        $influencer->delete();
    }

    public function listPublicInfluencers(): Collection
    {
        return Influencer::query()
            ->with(['reviews' => fn ($query) => $query->approved()->latest()])
            ->get();
    }

    public function showInfluencerBySlug(string $slug): ?array
    {
        $influencer = Influencer::query()
            ->where('slug', $slug)
            ->with(['reviews' => fn ($query) => $query->approved()->latest()])
            ->first();

        if (! $influencer) {
            return null;
        }

        return [
            'influencer' => $influencer,
            'other_influencers' => Influencer::query()
                ->where('slug', '!=', $slug)
                ->inRandomOrder()
                ->take(5)
                ->get(),
        ];
    }
}
