<?php

namespace App\Http\Requests\FriendshipRequest;

use Illuminate\Foundation\Http\FormRequest;

class FriendRequestRespondRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'request_id' => 'required|exists:friend_requests,uuid',
            'response' => 'required|in:accepted,declined,cancelled,unfriended',
        ];
    }
}