<?php

namespace App\Http\Controllers;

use App\Services\InfluencerService;
use App\Http\Resources\InfluencerResource;
use App\Http\Resources\InfluencerResourceCollection;

class InfluencerController extends Controller
{
    public function index(InfluencerService $influencerService)
    {
        return new InfluencerResourceCollection(
            $influencerService->listPublicInfluencers()
        );
    }

    public function show(InfluencerService $influencerService, string $slug)
    {
        $result = $influencerService->showInfluencerBySlug($slug);

        if (! $result) {
            return response()->json([
                'message' => 'Influencer not found.',
            ], 404);
        }

        return response()->json([
            'influencer' => new InfluencerResource($result['influencer']),
            'other_influencers' => new InfluencerResourceCollection($result['other_influencers']),
        ]);
    }
}
