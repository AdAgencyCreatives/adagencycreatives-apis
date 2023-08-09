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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required',
            'employement_type' => 'required|string|max:255',
            'industry_experience' => 'required|string|max:255',
            'media_experience' => 'required|string|max:255',
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
