<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'street_1' => 'sometimes|max:255',
            'street_2' => 'sometimes|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ];
    }
}
