<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',

            'resource_type' => 'required|in:agencies,creatives,jobs,applications,posts',
            'resource_id' => 'required',
        ];
    }
}
