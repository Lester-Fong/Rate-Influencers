<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'name' => $this->name,
            'influencer_rating' => $this->influencer_rating,
            'comment_rating' => $this->comment_rating,
            'is_approved' => $this->is_approved,
        ];
    }
}
