<?php

namespace App\Http\Requests\YearsOfExperience;

use App\Rules\UniqueYearsOfExperienceName;
use Illuminate\Foundation\Http\FormRequest;

class StoreYearsOfExperienceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueYearsOfExperienceName],
        ];
    }
}