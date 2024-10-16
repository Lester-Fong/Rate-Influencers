<?php

namespace App\Services;
use App\Models\Influencer;

class InfluencerService
{
    /**
     * Create a new class instance.
     */
    public function showInfluencerBySlug($slug)
    {
        $influencer = Influencer::where('slug', $slug)->with('comments')->first();
        if (!$influencer) {
            return [
                "error" => true,
                "message" => "This influencer is not yet recognized."
            ];
        } else {
            $other_influencers = Influencer::where('slug', '!=', $slug)->inRandomOrder()->take(5)->get();
        }
        return [$influencer, $other_influencers];

    }
}
