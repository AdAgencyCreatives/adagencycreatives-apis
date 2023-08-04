<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return false;
    }

    public function rules()
    {
        $allowedRoles = ['advisory', 'agency', 'creative'];

        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ];

    }
}
