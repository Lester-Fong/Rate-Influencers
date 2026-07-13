<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexInfluencerRequest extends FormRequest
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
            'search' => 'nullable|string|max:100',
        ];
    }
}
