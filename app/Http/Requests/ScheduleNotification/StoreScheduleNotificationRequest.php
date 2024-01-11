<?php

namespace App\Http\Requests\ScheduleNotification;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sender_id' => 'required|exists:users,uuid',
            'recipient_id' => 'required|exists:users,uuid',
            'post_id' => 'required|exists:posts,uuid',
            'status' => 'nullable',
            'type' => 'required',
            'notification_text' => 'required|string|max:255',
            'scheduled_at' => 'nullable|datetime',
        ];
    }
}
