<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInfluencerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:influencers,slug'],
            'bio' => 'nullable|string|max:2000',
            'profile_picture' => 'nullable|url:http,https|max:2048',
            'facebook_link' => 'nullable|url:http,https|max:2048',
            'youtube_link' => 'nullable|url:http,https|max:2048',
            'tiktok_link' => 'nullable|url:http,https|max:2048',
            'instagram_link' => 'nullable|url:http,https|max:2048',
        ];
    }
}
