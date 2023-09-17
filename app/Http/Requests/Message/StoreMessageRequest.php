<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'message' => 'required',
        ];
    }
}
