<?php

namespace App\Http\Requests\FriendshipRequest;

use Illuminate\Foundation\Http\FormRequest;

class FriendRequestSendRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'receiver_id' => 'required|exists:users,uuid',
        ];
    }
}
