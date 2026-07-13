<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'reviewer_name' => 'required|string|max:50',
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:500',
        ];
    }
}
