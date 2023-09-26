<?php

namespace App\Http\Requests\Creative;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreativeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'category_id' => 'sometimes|exists:categories,uuid',
            'years_of_experience' => 'required',
            'employment_type' => 'required',
            'industry_experience' => 'required|array',
            'industry_experience.*' => 'exists:industries,uuid',
            'media_experience' => 'required|array',
            'media_experience.*' => 'exists:medias,uuid',
            'strengths' => 'required|array',
            'strengths.*' => 'exists:strengths,uuid',
            'is_remote' => 'required|boolean',
            'is_hybrid' => 'required|boolean',
            'is_onsite' => 'required|boolean',
            'is_featured' => 'required|boolean',
            'is_urgent' => 'required|boolean',
        ];
    }
}
