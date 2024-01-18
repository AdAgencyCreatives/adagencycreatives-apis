<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'category_id' => 'required|exists:categories,uuid',
            'state_id' => 'nullable|exists:locations,uuid',
            'city_id' => 'nullable|exists:locations,uuid',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'string|max:255',
            'industry_experience' => 'required|array',
            'industry_experience.*' => 'exists:industries,uuid',
            'media_experience' => 'required|array',
            'media_experience.*' => 'exists:medias,uuid',
            'salary_range' => 'nullable',
            'apply_type' => 'required|string|max:255',
            'external_link' => 'nullable',
            'is_remote' => 'required|boolean',
            'is_hybrid' => 'required|boolean',
            'is_onsite' => 'required|boolean',
            'is_featured' => 'required|boolean',
            'is_urgent' => 'required|boolean',
            'expired_at' => 'nullable|date',
        ];
    }
}