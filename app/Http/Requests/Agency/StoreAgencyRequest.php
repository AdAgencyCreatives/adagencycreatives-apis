<?php

namespace App\Http\Requests\Agency;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'name' => 'required',
            'about' => 'required',
            'size' => 'required',
        ];
    }
}
