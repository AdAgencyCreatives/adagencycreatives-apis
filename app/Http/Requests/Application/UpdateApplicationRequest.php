<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'resume' => 'sometimes',
            'message' => 'sometimes',
            'status' => 'sometimes|in:pending,accepted,rejected,archived,recommended,shortlisted,hired',
        ];
    }
}
