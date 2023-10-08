<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequestRequest extends FormRequest
{
    public function authorize()
    {
        return false;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'state_id' => 'required|exists:locations,uuid',
            'city_id' => 'nullable|exists:locations,uuid',
            'status' => '',
        ];
    }
}
