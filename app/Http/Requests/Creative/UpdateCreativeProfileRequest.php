<?php

namespace App\Http\Requests\Creative;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreativeProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = User::where('uuid', $this->route('user'))->firstOrFail();

        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => [
                'required',
                Rule::unique('users')->ignore($user->id),
            ],

        ];
    }

    public function messages()
    {
        return [
            'username.unique' => 'The username is already taken. Please choose a different one.',

        ];
    }
}
