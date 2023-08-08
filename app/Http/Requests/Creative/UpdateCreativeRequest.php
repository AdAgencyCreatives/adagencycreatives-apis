<?php

namespace App\Http\Requests\Creative;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreativeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'years_of_experience' => 'sometimes',
            'type_of_work' => 'sometimes',
        ];
    }
}
