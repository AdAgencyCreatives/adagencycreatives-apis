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
        return [
            'user_id' => 'required|exists:users,uuid',
            'resource_type' => 'required',
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'mp4', 'avi', 'json']; // Add video file extensions here
                    $fileExtension = strtolower($value->getClientOriginalExtension());
                    if (! in_array($fileExtension, $allowedTypes)) {
                        $fail('Invalid file type. Allowed types: jpg, jpeg, png, pdf, doc, docx, mp4, avi');
                    }
                },
            ],
        ];
    }
}