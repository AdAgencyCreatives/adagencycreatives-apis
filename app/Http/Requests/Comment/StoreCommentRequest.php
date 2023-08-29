<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'parent_id' => 'nullable|exists:comments,uuid',
            'content' => 'required|string',
        ];
    }
}
