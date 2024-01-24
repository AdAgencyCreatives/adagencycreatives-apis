<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'body' => 'required|string',
        ];
    }
}