<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reviewer_name' => $this->reviewer_name,
            'rating' => $this->rating,
            'review' => $this->review,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'influencer' => $this->whenLoaded('influencer', fn () => [
                'id' => $this->influencer->id,
                'name' => $this->influencer->name,
                'slug' => $this->influencer->slug,
            ]),
        ];
    }
}
