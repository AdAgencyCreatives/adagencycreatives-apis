<?php

namespace App\Http\Requests\GroupMember;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupMemberRequest extends FormRequest
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
        ];
    }
}
