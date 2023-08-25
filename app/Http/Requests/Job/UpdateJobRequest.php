<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes',
            'employement_type' => 'sometimes|string|max:255',
            'industry_experience' => 'sometimes|string|max:255',
            'media_experience' => 'sometimes|string|max:255',
            'salary_range' => 'sometimes|string|max:255',
            'experience' => 'sometimes|string|max:255',
            'apply_type' => 'sometimes|string|max:255',
            'external_link' => 'sometimes|nullable|url',
            'is_remote' => 'sometimes|boolean',
            'is_hybrid' => 'sometimes|boolean',
            'is_onsite' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'is_urgent' => 'sometimes|boolean',
            'status' => 'sometimes|in:pending,approved,rejected,expired,filled,published,draft',
            'expired_at' => 'sometimes|date',
        ];
    }
}
