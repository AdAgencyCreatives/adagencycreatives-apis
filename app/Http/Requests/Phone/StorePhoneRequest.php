<?php

namespace App\Http\Requests\Phone;

use Illuminate\Foundation\Http\FormRequest;

class StorePhoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'label' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required',
        ];
    }
}
