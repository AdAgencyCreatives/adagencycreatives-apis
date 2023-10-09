<?php

namespace App\Http\Requests\PackageRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'employment_type' => 'required|string|max:255',
            'industry_experience' => 'required|array',
            'industry_experience.*' => 'exists:industries,uuid',
            'media_experience' => 'required|array',
            'media_experience.*' => 'exists:medias,uuid',
            'salary_range' => 'required|string|max:255',
        ];
    }
}
