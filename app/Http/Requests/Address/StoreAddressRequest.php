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
            'city_id' => 'required|exists:locations,uuid',
            'state_id' => 'required|exists:locations,uuid',
        ];
    }
}
