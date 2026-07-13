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

    public function listPublicInfluencers(?string $search = null): Collection
    {
        return Influencer::query()
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function showInfluencerBySlug(string $slug): ?Influencer
    {
        return Influencer::query()
            ->where('slug', $slug)
            ->with(['reviews' => fn ($query) => $query->approved()->latest()])
            ->first();
    }
}
