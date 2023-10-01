<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobInvitationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'receiver_id' => 'required|exists:users,uuid',
            'job_id' => 'required|exists:job_posts,uuid',
        ];
    }
}
