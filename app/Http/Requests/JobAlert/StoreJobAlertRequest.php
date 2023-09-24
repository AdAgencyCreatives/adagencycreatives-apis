<?php

namespace App\Http\Requests\JobAlert;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobAlertRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'category_id' => 'required|exists:categories,uuid',
            'status' => 'required|in:1,0',
        ];
    }
}
