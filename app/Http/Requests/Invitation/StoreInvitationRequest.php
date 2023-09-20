<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sender_id' => 'required|exists:users,uuid',
            'receiver_id' => 'required|exists:users,uuid',
            'group_id' => 'required|exists:groups,uuid',
        ];
    }
}
