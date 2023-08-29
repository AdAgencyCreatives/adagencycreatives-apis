<?php

namespace App\Http\Requests\Like;

use Illuminate\Foundation\Http\FormRequest;

class StoreLikeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'post_id' => 'required|exists:posts,uuid',
        ];
    }
}
