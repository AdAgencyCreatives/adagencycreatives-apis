<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => 'sometimes',
            'attachment_ids' => 'sometimes|nullable|array',
            'status' => 'sometimes|in:draft,published,archived',
        ];
    }
}
