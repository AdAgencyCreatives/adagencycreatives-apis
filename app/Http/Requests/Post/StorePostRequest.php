<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'group_id' => 'required|exists:groups,uuid',
            'content' => 'required',
            'attachment_ids' => 'sometimes|array',
            'attachment_ids.*' => 'exists:attachments,uuid',
        ];
    }
}
