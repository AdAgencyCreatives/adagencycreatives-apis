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
            'user_id' => 'required|exists:users,uuid',
            'educations' => 'required|array|min:1',
            'educations.*.degree' => 'required|string',
            'educations.*.college' => 'required|string',
            'educations.*.completed_at' => 'required|date',
        ];
    }
}
