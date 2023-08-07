<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $allowedRoles = ['advisory', 'agency', 'creative'];

        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role' => 'sometimes|in:' . implode(',', $allowedRoles),
        ];
    }
}
