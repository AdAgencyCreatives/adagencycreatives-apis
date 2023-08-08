<?php

namespace App\Http\Requests\Agency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes',
            'about' => 'sometimes',
            'size' => 'sometimes',
            'type_of_work' => 'sometimes',
        ];

    }
}
