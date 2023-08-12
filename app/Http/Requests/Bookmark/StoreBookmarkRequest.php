<?php

namespace App\Http\Requests\Bookmark;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookmarkRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,uuid',
            'resource_type' => 'required|in:agencies,creatives,jobs,applications,posts',
            'resource_id' => 'required',
        ];
    }
}
