<?php

namespace App\Http\Requests\Industry;

use App\Rules\UniqueIndustryName;
use Illuminate\Foundation\Http\FormRequest;

class StoreIndustryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueIndustryName],
        ];
    }
}
