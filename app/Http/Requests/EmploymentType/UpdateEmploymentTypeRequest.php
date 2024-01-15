<?php

namespace App\Http\Requests\EmploymentType;

use App\Rules\UniqueEmploymentTypeName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmploymentTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueEmploymentTypeName],
        ];
    }
}
