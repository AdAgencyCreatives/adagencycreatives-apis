<?php

namespace App\Http\Requests\YearsOfExperience;

use App\Rules\UniqueYearsOfExperienceName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateYearsOfExperienceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
        ];
    }
}