<?php

namespace App\Http\Requests\Resume;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'years_of_experience' => 'required|string|max:255',
            'about' => 'required|string|max:500',
            'industry_specialty' => 'nullable|string|max:255',
            'media_experience' => 'nullable|string|max:255',
        ];
    }
}
