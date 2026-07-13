<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexInfluencerRequest;
use App\Services\InfluencerService;
use App\Http\Resources\InfluencerResource;
use App\Http\Resources\InfluencerResourceCollection;

class InfluencerController extends Controller
{
    public function index(
        IndexInfluencerRequest $request,
        InfluencerService $influencerService
    ): InfluencerResourceCollection
    {
        return new InfluencerResourceCollection(
            $influencerService->listPublicInfluencers(
                $request->validated('search')
            )
        );
    }

    public function show(InfluencerService $influencerService, string $slug)
    {
        $influencer = $influencerService->showInfluencerBySlug($slug);

        if (! $influencer) {
            return response()->json([
                'message' => 'Influencer not found.',
            ], 404);
        }

        return new InfluencerResource($influencer);
    }
}
