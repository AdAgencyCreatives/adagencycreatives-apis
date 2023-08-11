<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttachmentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'resource_type' => 'required',
            'file' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                    $fileExtension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($fileExtension, $allowedTypes)) {
                        $fail('Invalid file type. Allowed types: jpg, jpeg, png, pdf, doc, docx');
                    }
                },
            ],

        ];

    }
}
