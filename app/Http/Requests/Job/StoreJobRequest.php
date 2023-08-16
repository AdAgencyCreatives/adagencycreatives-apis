<?php

namespace App\Http\Requests\Job;

use App\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize()
    {
        // $access = auth()->user()->can('jobs.create');
        // if (!$access) {
        //     throw new UnauthorizedException();
        // }

        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'category_id' => 'required|exists:categories,uuid',
            'address_id' => 'required|exists:addresses,uuid',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employement_type' => 'required|string|max:255',
            'industry_experience' => 'required|array',
            'industry_experience.*' => 'exists:industries,id',
            'media_experience' => 'required|array',
            'media_experience.*' => 'exists:industries,id',
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
