<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'resume_id' => 'required|exists:resumes,uuid',
            'experiences' => 'required|array|min:1',
            'experiences.*.title' => 'required|string',
            'experiences.*.company' => 'required|string',
            'experiences.*.description' => 'required|string',
            'experiences.*.started_at' => 'required|date',
            'experiences.*.completed_at' => 'nullable|date|after:experiences.*.started_at',
        ];

    }
}
