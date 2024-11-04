<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Influencer;
use App\Services\InfluencerService;
use App\Http\Resources\InfluencerResource;
use App\Http\Resources\InfluencerResourceCollection;
use Illuminate\Support\Facades\Log;

class InfluencerController extends Controller
{
    //

    public function index()
    {
        return new InfluencerResourceCollection(Influencer::get());
    }


    public function show (InfluencerService $influencerService, string $slug) {
        if (isset($slug)) {
           try {
                $response = $influencerService->showInfluencerBySlug($slug);
                if (isset($response['error'])) {
                    return response()->json(['error'=> true, 'message'=> 'Influencer not found'], 404);
                } else {
                    $influencer = $response[0];
                    $other_influencers = $response[1];
                    return response()->json(
                        data: 
                        [
                            'error'=> false, 
                            'influencer'=> new InfluencerResource($influencer), 
                            'other_influencers'=> new InfluencerResourceCollection($other_influencers)
                        ], 
                        status: 200);
                }
           } catch (\Throwable $th) {
                Log::debug(print_r($th->getMessage(), true));
                return response()->json(['error'=> true, 'message'=> 'Internal Server Error'], 500);
           }
        }
    }
}
