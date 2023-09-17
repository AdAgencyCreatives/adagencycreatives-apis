<?php

namespace App\Http\Requests\Strength;

use App\Rules\UniqueStrengthName;
use Illuminate\Foundation\Http\FormRequest;

class StoreStrengthRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueStrengthName],
        ];
    }
}
