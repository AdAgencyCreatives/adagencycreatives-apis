<?php

namespace App\Http\Requests\Education;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'degree' => 'sometimes|string|max:255',
            'college' => 'sometimes|string|max:255',
            'started_at' => 'sometimes|date',
            'completed_at' => 'sometimes|date|after_or_equal:started_at',
        ];
    }
}
