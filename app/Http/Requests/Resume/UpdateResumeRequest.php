<?php

namespace App\Http\Requests\Resume;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'years_of_experience' => 'sometimes|string|max:255',
            'about' => 'sometimes|string|max:500',
            'industry_specialty' => 'sometimes|string|max:255',
            'media_experience' => 'sometimes|string|max:255',
        ];
    }
}
