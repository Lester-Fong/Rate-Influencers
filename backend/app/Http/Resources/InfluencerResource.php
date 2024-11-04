<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InfluencerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'rating' => $this->rating,
            'profile_picture' => $this->profile_picture,
            'facebook_link' => $this->facebook_link,
            'youtube_link' => $this->youtube_link,
            'tiktok_link' => $this->tiktok_link,
            'instagram_link' => $this->instagram_link,
            'created_at' => Carbon::parse($this->created_at)->format('M j, Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('M j, Y'),
            'comments' => new CommentResourceCollection($this->comments),
        ];
    }
}
