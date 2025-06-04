<?php

namespace App\Http\Requests\User;

use App\Models\Faq;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'required',
            'order' => 'required|numeric',
        ];
    }
}