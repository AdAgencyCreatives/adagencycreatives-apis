<?php

namespace App\Http\Requests\Strength;

use App\Rules\UniqueStrengthName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStrengthRequest extends FormRequest
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