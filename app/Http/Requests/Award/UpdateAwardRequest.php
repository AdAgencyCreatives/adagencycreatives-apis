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
            'award_title' => 'sometimes|string|max:255',
            'award_year' => 'sometimes|integer',
            'award_work' => 'sometimes|string|max:255',
        ];
    }
}
