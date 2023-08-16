<?php

namespace App\Http\Requests\Education;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'resume_id' => 'required|exists:resumes,uuid',
            'educations' => 'required|array|min:1',
            'educations.*.degree' => 'required|string',
            'educations.*.college' => 'required|string',
            'educations.*.started_at' => 'required|date',
            'educations.*.completed_at' => 'nullable|date|after:educations.*.started_at',
        ];
    }
}
