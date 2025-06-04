<?php

namespace App\Http\Requests\Faq;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'sometimes|max:255',
            'description' => 'sometimes',
            'order' => 'sometimes|numeric',
        ];
    }
}