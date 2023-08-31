<?php

namespace App\Http\Requests\Location;

use App\Rules\UniqueLocationName;
use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255', new UniqueLocationName],
        ];
    }
}
