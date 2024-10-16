<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Influencer;
use App\Services\InfluencerService;

class InfluencerController extends Controller
{
    //

    public function index()
    {
        return Influencer::all();
    }


    public function show (InfluencerService $influencerService, string $slug) {
        if (isset($slug)) {
           try {
                $response = $influencerService->showInfluencerBySlug($slug);
                return response()->json($response);
           } catch (\Throwable $th) {
                return response()->json(['error'=> true, 'message'=> 'Internal Server Error'], 500);
           }
        }
    }
}
