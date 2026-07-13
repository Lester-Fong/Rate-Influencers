<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInfluencerRequest;
use App\Http\Requests\UpdateInfluencerRequest;
use App\Http\Resources\InfluencerResource;
use App\Http\Resources\InfluencerResourceCollection;
use App\Models\Influencer;
use App\Services\InfluencerService;

class AdminInfluencerController extends Controller
{
    public function index(InfluencerService $influencerService): InfluencerResourceCollection
    {
        return new InfluencerResourceCollection(
            $influencerService->listAdminInfluencers()
        );
    }

    public function store(
        StoreInfluencerRequest $request,
        InfluencerService $influencerService
    ) {
        $influencer = $influencerService->createInfluencer($request->validated());

        return (new InfluencerResource($influencer))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdateInfluencerRequest $request,
        Influencer $influencer,
        InfluencerService $influencerService
    ): InfluencerResource {
        return new InfluencerResource(
            $influencerService->updateInfluencer($influencer, $request->validated())
        );
    }

    public function destroy(
        Influencer $influencer,
        InfluencerService $influencerService
    ) {
        $influencerService->deleteInfluencer($influencer);

        return response()->noContent();
    }
}
