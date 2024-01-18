<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $validate = [
            'user_id' => 'required|exists:users,uuid',
            'resource_type' => 'required',
            'file' => [
                'required',
                'file',
                'max:40240'
            ],
        ];
        if (is_array(request()->file)) {
            $validate['file'] =  ['required', 'array', 'min:1', 'max:5'];
            $validate['file.*'] = ['required', 'file', 'max:40240'];
        }
        return $validate;
    }
}
