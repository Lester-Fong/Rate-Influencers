<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Influencer;

class InfluencerController extends Controller
{
    //

    public function index()
    {
        return Influencer::all();
    }


    public function show (string $slug) {
        if (isset($slug)) {
            $influencer = Influencer::where('slug', $slug)->with('comments')->first();
            $other_influencers = Influencer::where('slug', '!=', $slug)->inRandomOrder()->take(5)->get();

            return response()->json([$influencer,  $other_influencers]);
        }

        return response()->json(['message'=> 'This influencer is not yet recognized.'],404);

    }
}
