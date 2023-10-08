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
            'state_id' => 'required|exists:locations,uuid',
            'city_id' => 'nullable|exists:locations,uuid',
            'years_of_experience' => 'required|exists:years_of_experiences,uuid',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'required|string|max:255',
            'industry_experience' => 'required|array',
            'industry_experience.*' => 'exists:industries,uuid',
            'media_experience' => 'required|array',
            'media_experience.*' => 'exists:medias,uuid',
            'salary_range' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'apply_type' => 'required|string|max:255',
            'external_link' => 'nullable|url',
            'is_remote' => 'required|boolean',
            'is_hybrid' => 'required|boolean',
            'is_onsite' => 'required|boolean',
            'is_featured' => 'required|boolean',
            'is_urgent' => 'required|boolean',
            'expired_at' => 'required|date',
        ];
    }
}
