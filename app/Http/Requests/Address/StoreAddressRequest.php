<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'label' => 'required|string|max:255',
            'street_1' => 'required|string|max:255',
            'street_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ];
    }
}
