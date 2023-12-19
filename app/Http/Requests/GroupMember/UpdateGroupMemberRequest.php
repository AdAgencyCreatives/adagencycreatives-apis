<?php

namespace App\Http\Requests\GroupMember;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'group_id' => 'required|exists:groups,uuid',
        ];
    }
}
