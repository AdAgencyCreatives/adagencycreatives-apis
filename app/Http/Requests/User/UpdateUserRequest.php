<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    protected function prepareForValidation()
    {
        $uuid = $this->route('user');
        $user = User::where('uuid', $uuid)->first();
        $this->merge([
            'email_rules' => [
                'email',
                'sometimes',
                'unique:users,email,'.$user->id,
            ],
        ]);
    }

    public function rules()
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => $this->input('email_rules'),
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:advisor,agency,creative,admin,recruiter',
            'status' => 'sometimes|in:pending,active,inactive',
        ];
    }
}