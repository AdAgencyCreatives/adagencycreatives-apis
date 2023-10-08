<?php

namespace App\Http\Requests\PackageRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'state_id' => 'required|exists:locations,uuid',
            'city_id' => 'nullable|exists:locations,uuid',
            'title' => 'required|string|max:255',
        ];
    }
}
